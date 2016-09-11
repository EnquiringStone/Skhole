<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 30-Nov-15
 * Time: 21:43
 */
namespace AppBundle\Controller;

use AppBundle\Doctrine\SearchQuery;
use AppBundle\Entity\Course\Courses;
use AppBundle\Enum\CoursePageTypeEnum;
use AppBundle\Enum\CourseStateEnum;
use AppBundle\Enum\PageTypeEnum;
use AppBundle\Exception\CourseRemovedException;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeController extends Controller {
    /**
     * @Route("/", name="app_home_main")
     */
    public  function mainAction()
    {
        //Don't do shit
    }

    /**
     * @Route("/{_locale}/home/", name="app_home_dashboard_page")
     */
    public function dashboardAction(Request $request)
    {
        $login = false;
        $search = null;
        if(array_key_exists('login', $request->query->all())) {
            $login = $request->query->get('login');
        }
        if(array_key_exists('search', $request->query->all())) $search = $request->query->get('search');
        $request->getSession()->set('locale', $request->getLocale());

        $languages = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLanguages')->findAll();
        $levels = $this->getDoctrine()->getRepository('AppBundle:Course\CourseLevels')->findAll();

        return $this->render(':home/dashboard:search.html.twig', array(
            'languages' => $languages,
            'levels' => $levels,
            'search' => $search,
            'login' => $login,
            'courses' => $this->getRandomCourses(10),
            'courseCollection' => $this->getCourseCollectionsForUser()
        ));
    }

    /**
     * @Route("/{_locale}/home/study/", name="app_home_study_page")
     */
    public function studyAction(Request $request)
    {
        $studyService = $this->get('app.controller.study.service');
        if ($request->query->has('clear')) $studyService->reset();

        $criteria = $studyService->getCorrectCriteriaForSession();

        if ($criteria['courseId'] > 0)
        {
            $redirectPath = $criteria['redirectRoute'];
            unset($criteria['redirectRoute']);
            return $this->redirectToRoute($redirectPath, $criteria);
        }
        return $this->render(':home/study:study.default.html.twig');
    }

    /**
     * @Route("/{_locale}/home/study/{courseId}/", name="app_home_study_course_page")
     */
    public function studyCourseAction($courseId)
    {
        $studyService = $this->get('app.controller.study.service');
        $course = $this->validateSpecifiedCourseId($courseId);

        $studyService->switchToCourseId($courseId);
        $course->setViews($course->getViews() + 1);
        $this->getDoctrine()->getManager()->flush();

        return $this->studyPanelsAction($courseId, 'custom', 'start');
    }

    /**
     * @Route("/{_locale}/home/study/{courseId}/{pageType}/{name}/", name="app_home_study_panels_page")
     */
    public function studyPanelsAction($courseId, $pageType, $name)
    {
        $course = $this->validateSpecifiedCourseId($courseId);
        $studyService = $this->get('app.controller.study.service');
        $studyService->switchToNameAndPageType($courseId, $pageType, $name);

        $courseReview = $this->getDoctrine()->getRepository('AppBundle:Course\CourseReviews')->findOneBy($studyService->getCorrectCriteriaForUserOrSession($courseId, 'userInsertedId'));
        return $this->render(':home/study:study.html.twig', array(
            'name' => $name,
            'pageType' => $pageType,
            'course' => $course,
            'courseReview' => $courseReview,
            'coursePages' => $this->getStudyPaginationInformation($course)
        ));
    }

    /**
     * @Route("/{_locale}/home/study/{courseId}/{pageOrder}/", name="app_home_study_pages_page")
     */
    public function studyPagesAction($courseId, $pageOrder)
    {
        if ($pageOrder == 0) return $this->redirectToRoute('app_home_study_panels_page', array(
            'courseId' => $courseId,
            'pageType' => strtolower(PageTypeEnum::CUSTOM_TYPE),
            'name' => 'start'
        ));

        $course = $this->validateSpecifiedCourseId($courseId);
        $studyService = $this->get('app.controller.study.service');
        $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->findOneBy(array('courseId' => $courseId, 'pageOrder' => $pageOrder));
        if ($page == null) throw new AccessDeniedException();

        $studyService->switchToPageId($courseId, $page->getId());
        $criteria = array('page' => $page, 'totalPages' => $course->getCoursePages()->count(), 'coursePages' => $this->getStudyPaginationInformation($course));

        $criteria = array_merge($criteria, $studyService->getCorrectCriteriaForCoursePageType($page));
        if (CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::ExerciseType, $page->getPageType()->getType()))
        {
            $answeredQuestions = array();
            $report = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->findOneBy($studyService->getCorrectCriteriaForUserOrSession($courseId, 'userId'));
            if ($report != null) $answeredQuestions = $this->buildAnsweredQuestionArray($page->getId(), $report->getId());
            $criteria['answeredQuestions'] = $answeredQuestions;
        }

        return $this->render(':home/study:study.pages.html.twig', $criteria);
    }

    /**
     * @Route("/{_locale}/home/profile/", name="app_home_profile_page")
     */
    public function profileAction(Request $request)
    {
        $education = $this->getDoctrine()->getRepository('AppBundle:Education\Educations')->findOneBy(array('userId' => $this->getUser()->getId()));
        $createdCourses = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->getCountByUserId($this->getUser()->getId());

        $offset = 0;
        if ($request->query->has('offset')) $offset = $request->query->get('offset');
        if (!is_int($offset) || $offset < 0) $offset = 0;

        $limit = $this->getParameter('standard_query_limit');
        $maxPagination = $this->getParameter('standard_pagination_max');

        $messageRepository =$this->getDoctrine()->getRepository('AppBundle:Messages');

        $messages = $messageRepository->findBy(array('userId' => $this->getUser()->getId()), array('sendDateTime' => 'DESC'), $limit, $offset);
        $total = $messageRepository->getCountByUserId($this->getUser()->getId());

        return $this->render(':home/dashboard:profile.html.twig', array(
            'education' => $education,
            'createdCourses' => $createdCourses,
            'messages' => $messages,
            'limit' => $limit,
            'offset' => $offset,
            'total' => $total,
            'maxPages' => $maxPagination,
            'page' => $request->query->get('page')
        ));
    }

    /**
     * @Route("/{_locale}/home/getting-started/", name="app_home_getting_started_page")
     */
    public function gettingStartedAction()
    {
        return $this->render(':home/dashboard:getting.started.html.twig', array('subMenu' => 'introduction'));
    }

    /**
     * @Route("/{_locale}/home/getting-started/{page}", name="app_home_getting_started_custom_page")
     */
    public function gettingStartedCustomAction($page)
    {
        $validPages = array('introduction', 'followCourse', 'createCourse', 'courseReport');

        if(!in_array($page, $validPages))
            throw new \Exception('Page does not exists');

        return $this->render(':home/dashboard:getting.started.html.twig', array('subMenu' => $page));
    }

    /**
     * @Route("/{_locale}/ajax/", name="app_home_ajax_page")
     */
    public function otherAction()
    {
        //Don't do shit
    }

    private function getRandomCourses($amount)
    {
        $criteria = array('removed' => false, 'state' => $this->getDoctrine()->getRepository('AppBundle:Course\CourseStates')->findOneBy(array('stateCode' => 'OK')), 'isUndesirable' => false);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Course\Courses');

        $totalCourses = $repo->getCountByCriteria($criteria);

        if($totalCourses <= $amount)
            return $repo->findBy($criteria);

        $offset = rand(0, $totalCourses - $amount);

        return $repo->findBy($criteria, null, $amount, $offset);
    }

    private function getCourseCollectionsForUser()
    {
        if(!$this->isGranted('ROLE_USER')) return array();

        $collectionItems = $this->getDoctrine()->getRepository('AppBundle:Course\CourseCollectionItems')->findBy(array('userId' => $this->getUser()->getId()));

        $friendlyArray = array();
        foreach ($collectionItems as $item)
        {
            $friendlyArray[] = $item->getCourseId();
        }
        return $friendlyArray;
    }

    /**
     * @param $courseId
     * @return \AppBundle\Entity\Course\Courses
     * @throws CourseRemovedException
     */
    private function validateSpecifiedCourseId($courseId)
    {
        $course = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->find($courseId);
        if($course == null) throw new AccessDeniedException();
        if($course->getState()->getStateCode() != CourseStateEnum::Finished || $course->getPublishedDateTime() == null || $course->getPublishedDateTime() > new \DateTime())
            throw new AccessDeniedException();

        if($course->getRemoved()) throw new CourseRemovedException();

        return $course;
    }

    private function getStudyPaginationInformation(Courses $course)
    {
        $pages = array();
        foreach ($course->getCoursePages() as $page)
        {
            $pages[$page->getPageOrder()] = array('order' => $page->getPageOrder());
        }

        return $pages;
    }

    private function buildAnsweredQuestionArray($pageId, $reportId)
    {
        $answeredQuestions = $this->getDoctrine()->getRepository('AppBundle:Report\AnswerResults')->getAllAnsweredQuestionByCoursePage($reportId, $pageId);

        $friendlyArray = array();

        foreach ($answeredQuestions as $question)
        {
            $friendlyMultipleChoiceArray = array();
            foreach ($question->getMultipleChoiceAnswers()->toArray() as $answer)
                $friendlyMultipleChoiceArray[] = $answer->getAnswerId();

            $friendlyArray[$question->getQuestionId()] = array('answer' => $question->getAnswer(), 'multipleChoiceAnswers' => $friendlyMultipleChoiceArray);
        }

        return $friendlyArray;
    }
}