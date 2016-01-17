<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 12:45
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeachController extends Controller
{
    /**
     * @Route("/{_locale}/teach/my-courses", name="app_teach_my_courses_page")
     */
    public function myCoursesAction(Request $request)
    {

    }

    /**
     * @Route("/{_locale}/teach/my-learn-groups", name="app_teach_my_learn_groups")
     */
    public function myLearnGroupsAction(Request $request)
    {

    }
}