<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:45
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Course\CourseCards;
use AppBundle\Entity\Course\CoursePages;
use AppBundle\Entity\Course\Courses;
use AppBundle\Entity\Course\CourseSchedules;
use AppBundle\Enum\CourseStateEnum;
use AppBundle\Exception\DelayException;
use AppBundle\Util\SecurityHelper;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeachController extends Controller
{
    /**
     * @Route("/{_locale}/teach/my-courses/", name="app_teach_my_courses_page")
     */
    public function myCoursesAction(Request $request)
    {
        $offset = 0;
        if(array_key_exists('offset', $request->query->all()))
            $offset = $request->query->get('offset');

        $userId = $this->getUser()->getId();
        $courseRepo = $this->getDoctrine()->getRepository('AppBundle:Course\Courses');
        $limit = $this->getParameter('standard_query_limit');

        $criteria = array('userInsertedId' => $userId, 'removed' => false);

        $courses = $courseRepo->findBy($criteria, null, $limit, $offset);

        return $this->render('teach/my.courses.html.twig', array(
            'courses' => $courses,
            'totalCourses' => $courseRepo->getCountByCriteria($criteria),
            'limit' => $limit,
            'offset' => $offset,
            'maxPages' => $this->getParameter('standard_pagination_max')
            ));
    }

    /**
     * @Route("/{_locale}/teach/create/", name="app_teach_create_course_page")
     */
    public function createCourseAction(Request $request)
    {
        $entities = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->getRecords(array(), 0, 1,
            array('sortAttribute' => 'id', 'sortValue' => 'DESC'), $this->getUser()->getId());

        if(sizeof($entities['resultSet']) > 0)
        {
            $entity = $entities['resultSet'][0];
            if(!SecurityHelper::dateTimeDiff($entity->getInsertDateTime(), 5))
            {
                $exception = new DelayException('Need at least 5 minutes between creating courses');
                $exception->setDelayCount(5);
                throw $exception;
            }
        }

        //Make the most basic course
        $course = new Courses();
        $course->setIsUndesirable(false);
        $course->setState($this->getDoctrine()->getRepository('AppBundle:Course\CourseStates')->findOneBy(array('stateCode' => CourseStateEnum::InProgress)));
        $course->setRemoved(false);
        $course->setPages(0);
        $course->setViews(0);

        $this->getDoctrine()->getManager()->persist($course);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_teach_edit_course_page', array('id' => $course->getId()));
    }

    /**
     * @Route("/{_locale}/teach/edit/{id}/", name="app_teach_edit_course_page")
     */
    public function editCourseAction($id)
    {
        $course = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->find($id);
        if(SecurityHelper::hasEditRights($course, 'userInsertedId', $this->getUser()->getId()))
        {
            return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'type' => 'standard', 'name' => 'start'));
        }
        throw new AccessDeniedException();
    }

    /**
     * @Route("/{_locale}/teach/edit/{id}/{pageType}/{name}/", name="app_teach_edit_course_page_page")
     */
    public function editCoursePageAction($id, $pageType, $name, Request $request)
    {
        $course = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->find($id);
        if(SecurityHelper::hasEditRights($course, 'userInsertedId', $this->getUser()->getId()))
        {
            $viewName = explode('-', $name);
            $view = '';
            foreach($viewName as $item)
            {
                $view .= ucfirst($item);
            }

            $page = null;
            if($request->query->has('pageId'))
            {
                $pageId = $request->query->get('pageId');
                $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->find($pageId);
                if($page->getCourseId() != $course->getId())
                    throw new AccessDeniedException();
            }

            $function = 'create'.$view.ucfirst($pageType).'Page';
            return $this->$function($course, $pageType, $name, $page);
        }
        throw new AccessDeniedException();
    }

    /**
     * @Route("/{_locale}/teach/progression-course-member/", name="app_teach_progression_course_members")
     */
    public function progressionCourseMembers(Request $request)
    {
        $limit = $this->getParameter('standard_query_limit');
        $maxPages = $this->getParameter('standard_pagination_max');
        $offset = 0;

        $criteria = array('hasAccepted' => false, 'mentorUserId' => $this->getUser()->getId());
        $repo = $this->getDoctrine()->getRepository('AppBundle:Report\SharedReports');

        $mentorRequests = $repo->findBy($criteria, array('insertDateTime' => 'ASC'), $limit, $offset);
        $totalMentorRequests = $repo->getCountByCriteria($criteria);

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->getAllUsersByMentor($this->getUser()->getId(), 0, $limit, 'firstName', 'ASC');
        $totalUsers = $this->getDoctrine()->getRepository('AppBundle:User')->getUserCountByMentor($this->getUser()->getId());

        return $this->render(':teach:progression.course.members.html.twig', array(
            'mentorRequests' => $mentorRequests,
            'totalMentorRequests' => $totalMentorRequests,
            'limit' => $limit,
            'maxPages' => $maxPages,
            'offset' => $offset,
            'users' => $users,
            'totalUsers' => $totalUsers));
    }

    /**
     * @Route("/{_locale}/teach/show/report/{id}/", name="app_teach_show_report")
     */
    public function showReportAction($id)
    {
        return $this->showReportCustomAction($id, 'front');
    }

    /**
     * @Route("/{_locale}/teach/show/report/{id}/custom/{name}/", name="app_teach_show_report_custom")
     */
    public function showReportCustomAction($id, $name)
    {
        $validNames = array('front', 'overview', 'end');
        if(!in_array($name, $validNames)) throw new AccessDeniedException();

        $sharedReport = $this->validateSharedReport($id);

        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($sharedReport->getReport()->getId());

        $criteria = array('sharedReport' => $sharedReport, 'pages' => $pages, 'name' => $name, 'offset' => 0);
        if($name == 'end') $criteria['offset'] = sizeof($pages);

        return $this->render(':teach:student.report.details.html.twig', $criteria);
    }

    /**
     * @Route("/{_locale}/teach/show/report/{id}/{pageId}", name="app_teach_show_report_page")
     */
    public function showReportPageAction($id, $pageId)
    {
        $sharedReport = $this->validateSharedReport($id);

        $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->find($pageId);
        if($page == null || $page->getCourseId() != $sharedReport->getReport()->getCourseId() || $page->getPageType()->getType() != 'exercise')
            throw new AccessDeniedException();

        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($sharedReport->getReport()->getId());
        $questions = $this->getDoctrine()->getRepository('AppBundle:Report\AnswerResults')->getAllAnsweredQuestionByCoursePage($sharedReport->getReport()->getId(), $page->getId());

        $criteria = array('sharedReport' => $sharedReport, 'pages' => $pages, 'page' => $page, 'questions' => $questions, 'offset' => $page->getPageOrder() - 1);

        return $this->render(':teach:student.report.details.html.twig', $criteria);
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\Report\SharedReports
     */
    private function validateSharedReport($id)
    {
        $sharedReport = $this->getDoctrine()->getRepository('AppBundle:Report\SharedReports')->find($id);
        if($sharedReport == null || $sharedReport->getMentorUserId() != $this->getUser()->getId() || !$sharedReport->getHasAccepted())
            throw new AccessDeniedException();

        return $sharedReport;
    }

    protected function createCourseInformationStandardPage(Courses $course, $type, $name)
    {
        $languages = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLanguages')->findAll();
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tags')->findAll();
        $levels = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLevels')->findAll();

        return $this->render(':teach:course.edit.html.twig',
            array('course' => $course, 'languages' => $languages, 'tags' => $tags, 'type' => $type, 'name' => $name, 'levels' => $levels));
    }

    protected function createCardIntroductionStandardPage(Courses $course, $type, $name)
    {
        $courseCard = $course->getCourseCard();
        if($courseCard == null)
        {
            $courseCard = new CourseCards();
            $courseCard->setCourse($course);
            $course->setCourseCard($courseCard);

            $this->getDoctrine()->getManager()->persist($courseCard);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'courseCard' => $courseCard, 'type' => $type, 'name' => $name));
    }

    protected function createCardTeacherStandardPage(Courses $course, $type, $name)
    {
        $courseCard = $course->getCourseCard();
        if($courseCard == null)
            return $this->redirectToRoute('app_teach_edit_course_page_page', array('id' => $course->getId(), 'pageType' => 'standard', 'name' => 'card-introduction'));

        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'teachers' => $courseCard->getTeachers(), 'type' => $type, 'name' => $name));
    }

    protected function createCardScheduleStandardPage(Courses $course, $type, $name)
    {
        $schedule = $course->getCourseSchedule();

        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'schedule' => $schedule, 'type' => $type, 'name' => $name));
    }

    protected function createCardProviderStandardPage(Courses $course, $type, $name)
    {
        $courseCard = $course->getCourseCard();
        if($courseCard == null)
            return $this->redirectToRoute('app_teach_edit_course_page_page', array('id' => $course->getId(), 'pageType' => 'standard', 'name' => 'card-introduction'));

        return $this->render(':teach:course.edit.html.twig',
            array('course' => $course, 'courseCard' => $courseCard, 'providers' => $courseCard->getProviders(), 'type' => $type, 'name' => $name));
    }

    protected function createCourseAnnouncementStandardPage(Courses $course, $type, $name)
    {
        $announcements = $course->getCourseAnnouncements();
        $teachers = $this->getDoctrine()->getRepository('AppBundle:Teachers')->findBy(array('userInsertedId' => $this->getUser()->getId()));

        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'announcements' => $announcements, 'type' => $type, 'name' => $name, 'teachers' => $teachers));
    }

    protected function createCourseResourcesStandardPage(Courses $course, $type, $name)
    {
        $resources = $course->getCourseResources();

        return $this->render('teach/course.edit.html.twig', array('course' => $course, 'resources' => $resources, 'type' => $type, 'name' => $name));
    }

//    Custom

    protected function createStartCustomPage(Courses $course, $type, $name)
    {
        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'type' => $type, 'name' => $name));
    }

    protected function createTextInstructionCustomPage(Courses $course, $type, $name, CoursePages $page = null)
    {
        $params = array('course' => $course, 'type' => $type, 'name' => $name);
        if($page != null)
            $params['instruction'] = $page;

        return $this->render(':teach:course.edit.html.twig', $params);
    }

    protected function createTextVideoInstructionCustomPage(Courses $course, $type, $name, CoursePages $page = null)
    {
        $params = array('course' => $course, 'type' => $type, 'name' => $name);
        if($page != null)
            $params['instruction'] = $page;

        return $this->render(':teach:course.edit.html.twig', $params);
    }

    protected function createQuestionsCustomPage(Courses $course, $type, $name, CoursePages $page = null)
    {
        $params = array('course' => $course, 'type' => $type, 'name' => $name);
        if($page != null)
            $params['exercise'] = $page;

        return $this->render(':teach:course.edit.html.twig', $params);
    }
//Publish
    protected function createStartPublishPage(Courses $course, $type, $name)
    {
        $params = array('course' => $course, 'type' => $type, 'name' => $name);
        return $this->render(':teach:course.edit.html.twig', $params);
    }
}