<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 18:52
 */

namespace AppBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigExceptionController;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends TwigExceptionController
{
    public function findTemplate(Request $request, $format, $code, $showException)
    {
        return parent::findTemplate($request, $format, $code, $showException);
    }
}