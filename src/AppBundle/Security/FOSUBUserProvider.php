<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 11-Dec-15
 * Time: 15:19
 */

namespace AppBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setterId = $setter.'Id';
        $setterToken = $setter.'AccessToken';

        if(null !== $previousUser = $this->userManager->findUserBy(array($property => $username)))
        {
            $previousUser->$setterId(null);
            $previousUser->$setterToken(null);
            $this->userManager->updateUser($previousUser);
        }

        $user->$setterId($username);
        $user->$setterToken($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        if($user === null)
        {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setterId = $setter.'Id';
            $setterToken = $setter.'AccessToken';

            $user = $this->userManager->createUser();
            $user->$setterId($username);
            $user->$setterToken($response->getAccessToken());

            $user->setUsername($response->getFirstName() . $response->getLastName());
            $user->setEmail($response->getEmail());
            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($serviceName).'AccessToken';
        $user->$setter($response->getAccessToken());
        return $user;
    }
}