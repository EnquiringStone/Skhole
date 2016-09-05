<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 05-Sep-16
 * Time: 19:52
 */

namespace AppBundle\Controller;


use AppBundle\Util\FileHelper;
use AppBundle\Util\ValidatorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LocalJobsController
 * @package AppBundle\Controller
 *
 * This controller is meant for all scheduled jobs that need to be taken care off. For example removing all unused pictures. These
 * paths can only be access from localhost (127.0.0.1)
 */
class LocalJobsController extends Controller
{
    /**
     * @Route("/jobs/clean-pictures", name="app_local_jobs_clean_unused_pictures")
     */
    public function cleanAllUnusedPicturesAction()
    {
        $picturePaths = array();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $teachers = $this->getDoctrine()->getRepository('AppBundle:Teachers')->findAll();

        foreach ($users as $user)
        {
            if (!ValidatorHelper::isStringNullOrEmpty($user->getPicture()))
                $picturePaths[] = $user->getPicture();
        }

        foreach ($teachers as $teacher)
        {
            if(!ValidatorHelper::isStringNullOrEmpty($teacher->getPictureUrl()))
                $picturePaths[] = $teacher->getPictureUrl();
        }

        $fileHelper = new FileHelper();
        $directory = $fileHelper->getWebDir() . 'pictures';
        $di = new \RecursiveDirectoryIterator($directory);
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file)
        {
            $remove = true;
            if(is_file($filename))
            {
                foreach ($picturePaths as $picture)
                {
                    if (strpos($picture, $file->getFileName()) > 0) $remove = false;
                }
                if($remove)
                {
                    if (file_exists($filename)) unlink($filename);
                }
            }
        }
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file)
        {
            if(is_dir($filename) && (!$this->endsWith($filename, 'pictures\\.') &&
                    !$this->endsWith($filename, 'pictures/.')) && (!$this->endsWith($filename, 'pictures\\..') &&
                    !$this->endsWith($filename, 'pictures/..')))
            {
                $iterator = new \FilesystemIterator($filename);
                if(!$iterator->valid())
                    rmdir($filename);
            }
        }
        exit;
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) return true;

        return (substr($haystack, -$length) === $needle);
    }
}