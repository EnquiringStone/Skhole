<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:45
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Course\Courses;
use AppBundle\Enum\CourseStateEnum;
use AppBundle\Util\SecurityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        //here the magic
        print_r($id); exit;
    }

    /**
     * @Route("/{_locale}/teach/my-learn-groups/", name="app_teach_my_learn_groups")
     */
    public function myLearnGroupsAction(Request $request)
    {
        return $this->render('teach/my.learn.groups.html.twig');
    }
}