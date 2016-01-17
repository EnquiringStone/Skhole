<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 30-Nov-15
 * Time: 21:43
 */
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller {

    /**
     * @Route("/{_locale}/home/", name="app_home_dashboard_page")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render(':home:dashboard.html.twig');
    }

    /**
     * @Route("/{_locale}/search/", name="app_search_page")
     */
    public function searchAction(Request $request)
    {

    }

    /**
     * @Route("/{_locale}/home/getting-started/", name="app_home_getting_started_page")
     */
    public function gettingStartedAction(Request $request)
    {

    }

    /**
     * @Route("/{_locale}/home/other/", name="app_home_other_page")
     */
    public function otherAction(Request $request)
    {

    }
}