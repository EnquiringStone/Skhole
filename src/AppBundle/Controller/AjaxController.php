<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Feb-16
 * Time: 22:04
 */

namespace AppBundle\Controller;


use AppBundle\Util\FileHelper;
use AppBundle\Util\SecurityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AjaxController extends Controller
{
    /**
     * @Route("/{_locale}/secure/ajax/upload/picture", name="app_secure_ajax_upload_picture")
     */
    public function uploadPictureAction(Request $request)
    {
        if(SecurityHelper::hasUserContext($this->get('security.token_storage'))) {
            $data = array();
            foreach($request->files->all() as $file) {
                if($file[0] instanceof UploadedFile) {
                    $file = $file[0];

                    $helper = new FileHelper($file, $this->getUser(), 'pictures');
                    $file->move($helper->getAbsolutePath(), $helper->getName());
                    $data[] = $helper->getRelativePath() . $helper->getName();
                }
            }
            return new Response(json_encode($data), 200, array('Content-Type', 'application/json'));
        }
        throw new AccessDeniedException();
    }
}