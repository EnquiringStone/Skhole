<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:44
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Course\Courses;
use AppBundle\Enum\CoursePageTypeEnum;
use AppBundle\Enum\CourseStateEnum;
use AppBundle\Enum\PageTypeEnum;
use AppBundle\Util\ValidatorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;

class LearnController extends Controller
{
    /**
     * @Route("/{_locale}/learn/course-collection/", name="app_learn_course_collection_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function courseCollectionAction(Request $request)
    {
        $offset = 0;
        if(array_key_exists('offset', $request->query->all()))
            $offset = $request->query->get('offset');

        $limit = $this->getParameter('standard_query_limit');
        $maxPages = $this->getParameter('standard_pagination_max');
        $total = 0;

        $collection = array();
        if($this->isGranted(array('ROLE_USER')))
        {
            $userId = $this->getUser()->getId();
            $criteria = array('userId' => $userId);
            $sort = array('insertDateTime' => 'DESC');
            $repo = $this->getDoctrine()->getRepository('AppBundle:Course\CourseCollectionItems');

            $collection = $repo->findBy($criteria, $sort, $limit, $offset);
            $total = $repo->getCountByCriteria($criteria);
        }
        return $this->render(':learn:course.collection.html.twig', array(
            'collection' => $collection,
            'totalItems' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'maxPages' => $maxPages));
    }

    /**
     * @Route("/{_locale}/learn/study/", name="app_learn_study_page")
     */
    public function studyAction()
    {
        $session = $this->get('session');

        if($session->has('lastCourseId'))
        {
            try
            {
                $course = $this->validateSpecifiedCourseId($session->get('lastCourseId'));

                if($session->has('lastPageId'))
                {
                    $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->find($session->get('lastPageId'));
                    if($page != null && $page->getCourseId() == $course->getId())
                        return $this->redirectToRoute('app_learn_study_pages_page', array('courseId' => $course->getId(), 'pageOrder' => $page->getPageOrder()));
                }
                elseif ($session->has('name'))
                {
                    $name = $session->get('name');
                    if($name == 'start' || $name == 'final')
                    {
                        return $this->redirectToRoute('app_learn_study_panels_page', array('courseId' => $course->getId(), 'pageType' => 'custom', 'name' => $name));
                    }
                }
                else
                {
                    return $this->redirectToRoute('app_learn_study_course_page', array('courseId' => $course->getId()));
                }
            }
            catch (Exception $e)
            {
                //Do nothing
            }
        }
        return $this->render(':learn:study.default.html.twig');
    }

    /**
     * @Route("/{_locale}/learn/study/{courseId}/", name="app_learn_study_course_page")
     * @param $courseId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studyCourseAction($courseId)
    {
        $course = $this->validateSpecifiedCourseId($courseId);

        $session = $this->get('session');

        if($session->has('lastPageId')) $session->remove('lastPageId');
        if($session->has('name')) $session->remove('name');

        $session->set('lastCourseId', $course->getId());

        $course->setViews($course->getViews() + 1);
        $this->getDoctrine()->getEntityManager()->flush();

        return $this->studyPanelsAction($courseId, 'custom', 'start');
    }

    /**
     * @Route("/{_locale}/learn/study/{courseId}/{pageType}/{name}/", name="app_learn_study_panels_page")
     * @param $courseId
     * @param $pageType
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function studyPanelsAction($courseId, $pageType, $name)
    {
        $course = $this->validateSpecifiedCourseId($courseId);
        $session = $this->get('session');
        if($session->has('lastPageId')) $session->remove('lastPageId');

        if($this->isGranted('ROLE_USER'))
            $courseReview = $this->getDoctrine()->getRepository('AppBundle:Course\CourseReviews')->findOneBy(array('userInsertedId' => $this->getUser()->getId(), 'courseId' => $courseId));
        else
            $courseReview = $this->getDoctrine()->getRepository('AppBundle:Course\CourseReviews')->findOneBy(array('sessionId' => $this->get('session')->getId(), 'courseId' => $courseId));

        if(PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::STANDARD_TYPE, $pageType) ||
            PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::CUSTOM_TYPE, $pageType))
        {
            $session->set('name', $name);

            return $this->render(':learn:study.html.twig', array('name' => $name, 'pageType' => $pageType, 'course' => $course, 'courseReview' => $courseReview));
        }
        throw new AccessDeniedException();
    }

    /**
     * @param $courseId
     * @param $pageOrder
     *
     * @Route("{_locale}/learn/study/{courseId}/{pageOrder}/", name="app_learn_study_pages_page")
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function studyPagesAction($courseId, $pageOrder)
    {
        $course = $this->validateSpecifiedCourseId($courseId);
        $session = $this->get('session');

        if($pageOrder == 0)
        {
            return $this->redirectToRoute('app_learn_study_panels_page', array('courseId' => $courseId, 'pageType' => strtolower(PageTypeEnum::CUSTOM_TYPE), 'name' => 'start'));
        }

        $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->findOneBy(array('courseId' => $courseId, 'pageOrder' => $pageOrder));
        if($page == null) throw new AccessDeniedException();

        if($session->has('name')) $session->remove('name');
        $session->set('lastPageId', $page->getId());

        $criteria = array('page' => $page, 'totalPages' => $course->getCoursePages()->count());

        if(CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::ExerciseType, $page->getPageType()->getType()))
        {
            $answeredQuestions = array();

            if($this->isGranted('ROLE_USER'))
                $report = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->findOneBy(array('courseId' => $courseId, 'userId' => $this->getUser()->getId()));
            else
                $report = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->findOneBy(array('courseId' => $courseId, 'sessionId' => $this->get('session')->getId()));

            if($report != null)
                $answeredQuestions = $this->buildAnsweredQuestionArray($page->getId(), $report->getId());

            $criteria = array_merge($criteria, array('type' => 'exercise.', 'name' => '', 'answeredQuestions' => $answeredQuestions));
        }
        elseif(CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::TextType, $page->getPageType()->getType()))
        {
            $criteria = array_merge($criteria, array('type' => 'instruction.', 'name' => 'text.'));
        }
        else
        {
            if(!ValidatorHelper::isStringNullOrEmpty($page->getYoutubeUrl()) && !ValidatorHelper::isStringNullOrEmpty($page->getContents()))
            {
                $criteria = array_merge($criteria, array('type' => 'instruction.', 'name' => 'video.text.'));
            }
            elseif(!ValidatorHelper::isStringNullOrEmpty($page->getYoutubeUrl()) && ValidatorHelper::isStringNullOrEmpty($page->getContents()))
            {
                $criteria = array_merge($criteria, array('type' => 'instruction.', 'name' => 'video.'));
            }
            else
            {
                $criteria = array_merge($criteria, array('type' => 'instruction.', 'name' => 'text.'));
            }
        }
        return $this->render(':learn:study.pages.html.twig', $criteria);
    }

    /**
     * @Route("/{_locale}/learn/course-reports/", name="app_learn_course_reports")
     */
    public function courseReportsAction()
    {
        $order = array('id' => 'DESC');
        $limit = $this->getParameter('standard_query_limit');

        $repo = $this->getDoctrine()->getRepository('AppBundle:Report\Reports');

        if($this->isGranted('ROLE_USER'))
        {
            $courseReports = $repo->findBy(array('userId' => $this->getUser()->getId(), 'isComplete' => true), $order, $limit, 0);
            $totalPages = $repo->getCountByCriteria(array('userId' => $this->getUser()->getId(), 'isComplete' => true));
        }
        else
        {
            $courseReports = $repo->findBy(array('sessionId' => $this->get('session')->getId(), 'isComplete' => true), $order, $limit, 0);
            $totalPages = $repo->getCountByCriteria(array('sessionId' => $this->get('session')->getId(), 'isComplete' => true));
        }

        return $this->render(':learn:course.reports.html.twig', array(
            'courseReports' => $courseReports,
            'limit' => $limit, 'offset' => 0,
            'maxPages' => $this->getParameter('standard_pagination_max'),
            'totalReports' => $totalPages));
    }

    /**
     * @param $courseId
     * @return \AppBundle\Entity\Course\Courses
     */
    private function validateSpecifiedCourseId($courseId)
    {
        $course = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->find($courseId);
        if($course == null) throw new AccessDeniedException();
        if($course->getState()->getStateCode() != CourseStateEnum::Finished || $course->getRemoved() || $course->getPublishedDateTime() == null || $course->getPublishedDateTime() > new \DateTime())
            throw new AccessDeniedException();

        return $course;
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