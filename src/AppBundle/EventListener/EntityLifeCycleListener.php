<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 17-Jan-16
 * Time: 14:02
 */

namespace AppBundle\EventListener;


use AppBundle\Interfaces\Entity\UserStatisticsInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\DateTime;

class EntityLifeCycleListener implements EventSubscriber
{

    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(TokenStorage $storage)
    {

        $this->storage = $storage;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleUserStatistics($args, false);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->handleUserStatistics($args, true);
    }

    private function handleUserStatistics(LifecycleEventArgs $args, $isUpdate)
    {
        if(!$this->validateUser($this->storage->getToken()->getUser()))
            return;

        $entity = $args->getEntity();

        if($entity instanceof UserStatisticsInterface)
        {
            if($isUpdate)
            {
                $entity->setUpdateDateTime(new \DateTime());
                $entity->setUserUpdatedId($this->storage->getToken()->getUser()->getId());
            }
            else
            {
                $entity->setInsertDateTime(new \DateTime());
                $entity->setUserInsertedId($this->storage->getToken()->getUser()->getId());
            }
        }
    }

    private function validateUser($user)
    {
        return $user != null;
    }
}