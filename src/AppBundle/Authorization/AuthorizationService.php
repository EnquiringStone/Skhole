<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 20:19
 */

namespace AppBundle\Authorization;


use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AuthorizationService
{
    /**
     * @var TokenStorage
     */
    private $storage;
    /**
     * @var AuthorizationChecker
     */
    private $checker;

    function __construct(TokenStorage $storage, AuthorizationChecker $checker)
    {

        $this->storage = $storage;
        $this->checker = $checker;
    }

    /**
     * Checks if the user is logged in
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->checker->isGranted('ROLE_USER');
    }

    /**
     * @return User
     */
    public function getAuthorizedUserOrThrowException()
    {
        if($this->isAuthorized())
        {
            $user = $this->storage->getToken()->getUser();
            if($user instanceof User)
                return $user;
            throw new AccessDeniedException();
        }
        throw new AccessDeniedException();
    }

    /**
     * @param        $entity
     * @param string $attribute
     * @return bool
     */
    public function canEditEntity($entity, $attribute = 'userId')
    {
        if($this->isAuthorized())
        {
            $getter = 'get' . ucfirst($attribute);
            $user = $this->getAuthorizedUserOrThrowException();
            return $entity->$getter() == $user->getId();
        }
        return false;
    }
}