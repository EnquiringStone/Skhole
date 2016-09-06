<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:44
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}