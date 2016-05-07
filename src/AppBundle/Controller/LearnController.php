<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:44
 */

namespace AppBundle\Controller;


use AppBundle\Enum\CourseStateEnum;
use AppBundle\Enum\PageTypeEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        return $this->render(':learn:study.default.html.twig');
    }

    /**
     * @Route("/{_locale}/learn/study/{courseId}/", name="app_learn_study_course_page")
     */
    public function studyCourseAction($courseId)
    {
        $course = $this->validateSpecifiedCourseId($courseId);

        $course->setViews($course->getViews() + 1);
        $this->getDoctrine()->getEntityManager()->flush();

        return $this->studyPanelsAction($courseId, 'custom', 'start');
    }

    /**
     * @Route("/{_locale}/learn/study/{courseId}/{pageType}/{name}/", name="app_learn_study_panels_page")
     */
    public function studyPanelsAction($courseId, $pageType, $name)
    {
        $course = $this->validateSpecifiedCourseId($courseId);

        if(PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::STANDARD_TYPE, $pageType) ||
            PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::CUSTOM_TYPE, $pageType))
        {
            return $this->render(':learn:study.html.twig', array('name' => $name, 'pageType' => $pageType, 'course' => $course));
        }
        throw new AccessDeniedException();
    }

    /**
     * @Route("/{_locale}/learn/course-reports/", name="app_learn_course_reports")
     */
    public function courseReportsAction(Request $request)
    {
        return $this->render(':learn:course.reports.html.twig');
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
}