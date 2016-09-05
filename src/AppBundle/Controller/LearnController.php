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
use Doctrine\Common\Collections\ArrayCollection;
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
    public function studyAction(Request $request)
    {
        $session = $this->get('session');

        if($request->query->has(('clear')))
        {
            if($session->has('lastCourseId')) $session->remove('lastCourseId');
            if($session->has('lastPageId')) $session->remove('lastPageId');
            if($session->has('name')) $session->remove('name');
            if($session->has('pageType')) $session->remove('pageType');
        }

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
                    $pageType = $session->get('pageType');
                    return $this->redirectToRoute('app_learn_study_panels_page', array('courseId' => $course->getId(), 'pageType' => $pageType, 'name' => $name));
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
        return $this->render(':learn:study.default.html.twig', array(
            'courses' => $this->getRandomCourses(10),
            'courseCollection' => $this->getCourseCollectionsForUser()
        ));
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
            $session->set('pageType', $pageType);

            return $this->render(':learn:study.html.twig', array('name' => $name, 'pageType' => $pageType, 'course' => $course, 'courseReview' => $courseReview,
                'coursePages' => $this->getStudyPaginationInformation($course)));
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
        if($session->has('pageType')) $session->remove('pageType');
        $session->set('lastPageId', $page->getId());

        $criteria = array('page' => $page, 'totalPages' => $course->getCoursePages()->count(), 'coursePages' => $this->getStudyPaginationInformation($course));

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
     * @Route("/{_locale}/learn/course-reports/{id}/", name="app_learn_course_report_details")
     */
    public function courseReportDetailsAction($id)
    {
        return $this->courseReportDetailsCustomAction($id, 'front');
    }

    /**
     * @Route("/{_locale}/learn/course-reports/{id}/{pageId}", name="app_learn_course_report_details_page")
     */
    public function courseReportDetailPageAction($id, $pageId)
    {
        $report = $this->validateReportDetails($id);
        $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->find($pageId);
        if($page == null || $page->getCourseId() != $report->getCourseId() || $page->getPageType()->getType() != 'exercise')
            throw new AccessDeniedException();

        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($report->getId());
        $questions = $this->getDoctrine()->getRepository('AppBundle:Report\AnswerResults')->getAllAnsweredQuestionByCoursePage($report->getId(), $page->getId());

        $index = 0;
        foreach ($pages as $aPage) {
            if ($aPage['id'] == $page->getId())
                break;
            $index ++;
        }

        $criteria = array('report' => $report, 'pages' => $pages, 'page' => $page, 'questions' => $questions, 'offset' => $pages[$index]['realOffset'] - 1);

        return $this->render(':learn:course.report.details.html.twig', $criteria);
    }

    /**
     * @Route("/{_locale}/learn/course-reports/{id}/custom/{name}", name="app_learn_course_report_details_custom")
     */
    public function courseReportDetailsCustomAction($id, $name)
    {
        $validNames = array('front', 'overview', 'end');
        if(!in_array($name, $validNames)) throw new AccessDeniedException();
        $report = $this->validateReportDetails($id);

        $pages = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->getAllPagesByReport($report->getId());

        $criteria = array('report' => $report, 'pages' => $pages, 'name' => $name, 'offset' => 0);
        if($name == 'end') $criteria['offset'] = sizeof($pages);

        return $this->render(':learn:course.report.details.html.twig', $criteria);
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\Report\Reports
     */
    private function validateReportDetails($id)
    {
        $courseReport = $this->getDoctrine()->getRepository('AppBundle:Report\Reports')->find($id);
        if($courseReport != null)
        {
            if($this->isGranted('ROLE_USER'))
            {
                if($courseReport->getUserId() != $this->getUser()->getId()) throw new AccessDeniedException();
            }
            else
            {
                if($courseReport->getSessionId() != $this->get('session')->getId()) throw new AccessDeniedException();
            }

            return $courseReport;
        }
        throw new AccessDeniedException();
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

    private function getStudyPaginationInformation(Courses $course)
    {
        $pages = array();
        foreach ($course->getCoursePages() as $page)
        {
            $pages[$page->getPageOrder()] = array('order' => $page->getPageOrder());
        }

        return $pages;
    }
}