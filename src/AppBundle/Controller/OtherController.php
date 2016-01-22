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
    public function frequentlyAskedQuestionsAction(Request $request)
    {
        return $this->render('other/faq.html.twig');
    }

    /**
     * @Route("/{_locale}/tips/", name="app_other_tips_page")
     */
    public function tipsAndTricksAction(Request $request)
    {
        return $this->render('other/tips.tricks.html.twig');
    }

    /**
     * @Route("/{_locale}/copyright/", name="app_other_copyright_page")
     */
    public function copyrightAction(Request $request)
    {
        return $this->render('more/copyright.html.twig');
    }

    /**
     * @Route("/{_locale}/conditions/", name="app_other_conditions_page")
     */
    public function conditionsAction(Request $request)
    {
        return $this->render('other');
    }

    /**
     * @Route("/{_locale}/help/", name="app_other_help_page")
     */
    public function helpAction(Request $request)
    {
        return $this->render('');
    }

    /**
     * @Route("/{_locale}/about-us/", name="app_other_about_us")
     */
    public function aboutUsAction(Request $request)
    {
        return $this->render('more/about.us.html.twig');
    }
}