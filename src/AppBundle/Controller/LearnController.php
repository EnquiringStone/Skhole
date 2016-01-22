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
        return $this->render(':learn:course.collection.html.twig');
    }

    /**
     * @Route("/{_locale}/learn/study/", name="app_learn_study_page")
     */
    public function studyAction(Request $request)
    {
        return $this->render(':learn:study.html.twig');
    }
}