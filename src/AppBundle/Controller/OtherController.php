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
}