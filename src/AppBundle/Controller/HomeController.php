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
     * @Route("/{_locale}/", name="app_dashboard_page")
     */
    public function dashboardAction(Request $request)
    {
        return $this->render(':home:dashboard.html.twig');
    }
}