<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 14:11
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OtherController extends Controller
{

    /**
     * @Route("/{_locale}/faq/", name="app_other_frequently_asked_questions_page")
     */
    public function frequentlyAskedQuestionsAction()
    {
        return $this->render(':other:faq.html.twig');
    }

    /**
     * @Route("/{_locale}/copyright/", name="app_other_copyright_page")
     */
    public function copyrightAction()
    {
        return $this->render(':other:copyright.html.twig');
    }

    /**
     * @Route("/{_locale}/about-us/", name="app_other_about_us")
     */
    public function aboutUsAction()
    {
        return $this->render(':other:about.us.html.twig');
    }

    /**
     * @Route("/{_locale}/contact/", name="app_other_contact")
     */
    public function contactAction()
    {
        $subjects = $this->getDoctrine()->getRepository('AppBundle:EmailSubjects')->findAll();

        return $this->render(':other:contact.html.twig', array('subjects' => $subjects));
    }

    /**
     * @Route("/{_locale}/guidelines/", name="app_other_guidelines")
     */
    public function guidelinesAction()
    {
        return $this->render(':other:guidelines.html.twig');
    }

    /**
     * @Route("/{_locale}/legal/", name="app_other_legal")
     */
    public function legalAction(Request $request)
    {
        $page = $request->query->has('page') ? $request->query->get('page') : null;

        return $this->render(':other:legal.html.twig', array('page' => $page));
    }

    /**
     * @Route("/{_locale}/report/", name="app_other_report")
     */
    public function giveNoticeAction(Request $request)
    {
        $allParams = $request->query->all();

        $course = null;
        $page = null;
        $coursePart = 'other';

        if(array_key_exists('course', $allParams)) $course = $this->getDoctrine()->getRepository('AppBundle:Course\Courses')->find($allParams['course']);
        if(array_key_exists('page', $allParams)) $page = $this->getDoctrine()->getRepository('AppBundle:Course\CoursePages')->find($allParams['page']);
        if(array_key_exists('coursePart', $allParams)) $coursePart = $allParams['coursePart'];

        if($page != null) $course = $page->getCourse();

        if($course != null && ($course->getIsUndesirable() || $course->getState()->getStateCode() != 'OK' || $course->getRemoved()))
            throw new AccessDeniedException();

        return $this->render(':other:give.notice.html.twig', array('course' => $course, 'page' => $page, 'coursePart' => $coursePart));
    }
}