<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 11-Dec-15
 * Time: 15:19
 */

namespace AppBundle\Security;

use AppBundle\Util\RandomStringGenerator;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    protected $mentorCodeLength = 10;

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

            $user->setUsername($response->getUsername());
            $user->setInsertDateTime(new \DateTime());
            if($response->getFirstName() == null && $response->getLastName() == null && $response->getRealName() != null)
            {
                $name = explode(" ", $response->getRealName());

                if(count($name) >= 1)
                    $user->setFirstName($name[0]);
                if(count($name) >= 2)
                    $user->setSurname(implode(" ", array_slice($name, 1)));
            } else {
                $user->setFirstName($response->getFirstName());
                $user->setSurname($response->getLastName());
            }
            if($response->getEmail() == null)
            {
                $user->setEmail(uniqid("EmailNotUsed", true));
            } else {
                $user->setEmail($response->getEmail());
                $user->setCustomEmail($response->getEmail());
            }
            $user->setNickname($response->getNickname());
            $user->setRealName($response->getRealName());
            $user->setPassword($username);
            $user->setRoles(array('ROLE_USER'));
            $user->setEnabled(true);
            $generator = new RandomStringGenerator();
            $user->setMentorCode($generator->generate($this->mentorCodeLength));
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