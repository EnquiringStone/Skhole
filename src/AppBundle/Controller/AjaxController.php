<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Feb-16
 * Time: 22:04
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends Controller
{
    /**
     * @Route("/{_locale}/secure/ajax/upload/picture", name="app_secure_ajax_upload_picture")
     */
    public function uploadPictureAction(Request $request)
    {
        $fp = fopen(__DIR__.'/../../../web/pictures/test.jpg', 'w');
        fwrite($fp, $request->request->get('data'));
        fclose($fp);

        print_r('jeej'); exit;
    }
}