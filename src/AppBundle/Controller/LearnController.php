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
use Symfony\Component\Routing\Annotation\Route;

class LearnController extends Controller
{
    /**
     * @Route("/{_locale}/learn/course-collection/", name="app_learn_course_collection_page")
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
        return $this->render(':learn:study.html.twig');
    }

    /**
     * @Route("/{_locale}/learn/course-reports/", name="app_learn_course_reports")
     */
    public function courseReportsAction(Request $request)
    {
        return $this->render(':learn:course.reports.html.twig');
    }
}