<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 05-Sep-16
 * Time: 19:52
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Course\CourseCategories;
use AppBundle\Util\FileHelper;
use AppBundle\Util\ValidatorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/jobs/database/categories", name="app_local_jobs_add_categories_to_database")
     */
    public function addCategoriesToDatabase(Request $request)
    {
        if (!$this->isLocalHost($request))
            return $this->redirectToRoute('app_home_dashboard_page', array('_locale' => $request->getLocale()));

        $this->addCategory('Architecture', 'db.categories.category.architecture');
        $this->addCategory('Art and Culture', 'db.categories.category.art.culture');
        $this->addCategory('Biology and Life science', 'db.categories.category.biology.life.science');
        $this->addCategory('Business and Management', 'db.categories.category.business.management');
        $this->addCategory('Chemistry', 'db.categories.category.chemistry');
        $this->addCategory('Communication', 'db.categories.category.communication');
        $this->addCategory('Computer science', 'db.categories.category.computer.science');
        $this->addCategory('Design', 'db.categories.category.design');
        $this->addCategory('Economics and Finance', 'db.categories.category.economics.finance');
        $this->addCategory('History', 'db.categories.category.history');
        $this->addCategory('Humanities', 'db.categories.category.humanities');
        $this->addCategory('Language', 'db.categories.category.language');
        $this->addCategory('Law', 'db.categories.category.law');
        $this->addCategory('Literature', 'db.categories.category.literature');
        $this->addCategory('Mathematics', 'db.categories.category.mathematics');
        $this->addCategory('Medicine', 'db.categories.category.medicine');
        $this->addCategory('Music', 'db.categories.category.music');
        $this->addCategory('Philosophy', 'db.categories.category.philosophy');
        $this->addCategory('Science', 'db.categories.category.science');
        $this->addCategory('Social science', 'db.categories.category.social.science');
        $this->addCategory('Other', 'db.categories.category.other');

        $this->getDoctrine()->getEntityManager()->flush();
        exit;
    }

    private function addCategory($categoryName, $translation)
    {
        $category = new CourseCategories();
        $category->setCategory($categoryName);
        $category->setTranslation($translation);

        $this->getDoctrine()->getEntityManager()->persist($category);
    }

    /**
     * @Route("/jobs/clean-pictures", name="app_local_jobs_clean_unused_pictures")
     */
    public function cleanAllUnusedPicturesAction(Request $request)
    {
        if(!$this->isLocalHost($request))
        {
            return $this->redirectToRoute('app_home_dashboard_page', array('_locale' => $request->getLocale()));
        }


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

    private function isLocalHost(Request $request)
    {
        $whiteList = array('127.0.0.1', '::1', 'localhost');
        return in_array($request->getClientIp(), $whiteList);
    }
}