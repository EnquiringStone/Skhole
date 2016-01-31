<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 23:45
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Entity\Messages;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessagesAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(EntityManager $manager, TokenStorage $storage)
    {
        $this->manager = $manager;
        $this->storage = $storage;
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Messages Ajax Service 1
        return 'MAS1';
    }

    public function deleteById($args)
    {
        if(!key_exists('ids', $args) || count($args['ids']) <= 0)
            throw new \Exception('Unknown id');

        //TODO uncomment & translations
        foreach($args['ids'] as $id)
        {
            $entity = $this->manager->getRepository('AppBundle:Messages')->find($id);
            if($entity == null)
                return;
            if($this->hasRights($entity))
            {
                $this->manager->remove($entity);
            }
        }
        $this->manager->flush();
    }

    public function readById($args)
    {
        if(!key_exists('ids', $args) || count($args['ids']) <= 0)
            throw new \Exception('Unknown id');

        foreach($args['ids'] as $id)
        {
            $entity = $this->manager->getRepository('AppBundle:Messages')->find($id);
            if($this->hasRights($entity))
            {
                $entity->setIsRead(true);
                $this->manager->persist($entity);
            }
        }
        $this->manager->flush();
    }

    private function hasRights($entity)
    {
        if($this->storage->getToken() == null || $this->storage->getToken()->getUser() == null)
            throw new AccessDeniedException('Nope');
        if($entity instanceof Messages)
        {
            return $this->storage->getToken()->getUser()->getId() == $entity->getUserId();
        }
        return false;
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('deleteById',
            'readById');
    }
}