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
use AppBundle\Util\SecurityHelper;
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
                //TODO Create page (or redirect to existing page) and explain that you need 5 minutes between creating courses
                throw new \Exception('Need at least 5 minutes between creating courses');
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
     * @Route("/{_locale}/teach/my-learn-groups/", name="app_teach_my_learn_groups")
     */
    public function myLearnGroupsAction(Request $request)
    {
        return $this->render('teach/my.learn.groups.html.twig');
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

        return $this->render(':teach:course.edit.html.twig', array('course' => $course, 'announcements' => $announcements, 'type' => $type, 'name' => $name));
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