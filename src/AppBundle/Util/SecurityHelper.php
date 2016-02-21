<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 21-Feb-16
 * Time: 16:19
 */

namespace AppBundle\Util;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SecurityHelper
{
    public static function hasEditRights($entity, $attribute = 'userId', $userId = 0)
    {
        if($userId > 0)
        {
            $getter = "get".ucfirst($attribute);
            return $entity->$getter() == $userId;
        }
        return true;
    }

    public static function hasUserContext(TokenStorage $tokenStorage)
    {
        return $tokenStorage != null && $tokenStorage->getToken() != null && $tokenStorage->getToken()->getUser() != null;
    }

    public static function dateTimeDiff(\DateTime $dateTime, $minutes)
    {
        $interval = $dateTime->diff(new \DateTime());

        if($minutes > 59)
            throw new \Exception('Minutes has to be below 60');

        return !($interval->y == 0 && $interval->m == 0 && $interval->d == 0 && $interval->h == 0 && $interval->i < $minutes);
    }
}