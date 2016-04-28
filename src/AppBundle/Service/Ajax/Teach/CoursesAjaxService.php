<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Dec-15
 * Time: 19:46
 */

namespace AppBundle\Service\Ajax\Teach;


use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Util\PageControlHelper;
use AppBundle\Util\SecurityHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CoursesAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TokenStorage
     */
    private $storage;
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var
     */
    private $limit;
    /**
     * @var
     */
    private $pages;

    public function __construct(EntityManager $manager, TokenStorage $storage, \Twig_Environment $environment, $limit, $pages)
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->environment = $environment;
        $this->limit = $limit;
        $this->pages = $pages;
    }

    public function deleteCourseByIds($args)
    {
        if(!array_key_exists('ids', $args))
            throw new \Exception('ids needs to be defined');

        if(!SecurityHelper::hasUserContext($this->storage))
            throw new AccessDeniedException();

        foreach($args['ids'] as $id)
        {
            $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($id);
            if($entity != null)
            {
                if(SecurityHelper::hasEditRights($entity, 'userInsertedId', $this->storage->getToken()->getUser()->getId()))
                {
                    $entity->setRemoved(true);
                    $this->manager->persist($entity);
                }
            }
        }
        $this->manager->flush();
    }

    public function getReviewModals($args)
    {
        $this->validate($args);
        if(!array_key_exists('limit', $args)) $args['limit'] = $this->limit;
        if(!array_key_exists('offset', $args)) $args['offset'] = 0;

        if(!array_key_exists('defaultValues', $args)) $args['defaultValues'] = array();
        else $args['defaultValues'] = PageControlHelper::createDefaultSearch($args['defaultValues']);

        $repo = $this->manager->getRepository('AppBundle:Course\Courses');

        if(array_key_exists('searchAttributes', $args))
        {
            $resultSet = $repo->getRecordsBySearch($args['offset'], $args['limit'], $this->createSort($args),
                $args['searchAttributes'],  $args['context'] == 'SELF' ? $this->storage->getToken()->getUser()->getId() : 0);
        }
        else
        {
            $resultSet = $repo->getRecords($args['defaultValues'], $args['offset'], $args['limit'], $this->createSort($args),
                $args['context'] == 'SELF' ? $this->storage->getToken()->getUser()->getId() : 0);
        }

        $html = '';
        $index = 1;
        foreach($resultSet['resultSet'] as $result)
        {
            $html .= $this->environment->render(':modal/my-courses:course.reviews.modal.html.twig', array(
                'course' => $result,
                'modalId' => 'courseReviewsModal'.$index,
                'limit' => $this->limit,
                'maxPages' => $this->pages
            ));
            $index ++;
        }
        return array('html' => $html);
    }

    private function validate($args)
    {
        if($args['context'] == 'SELF' && ($this->storage->getToken() == null || $this->storage->getToken()->getUser() == null))
            throw new \Exception('context is self but no user context found');
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Course(S) Ajax Service 1
        return 'CSAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('getReviewModals', 'deleteCourseByIds');
    }

    private function createSort($args)
    {
        if(array_key_exists('sortAttribute',$args) && array_key_exists('sortValue', $args))
            return array('sortAttribute' => $args['sortAttribute'], 'sortValue' => $args['sortValue']);
        return null;
    }
}