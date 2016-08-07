<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 07-Aug-16
 * Time: 22:08
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class CookieAjaxService implements AjaxInterface
{

    /**
     * @var Session
     */
    private $session;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;
    /**
     * @var EntityManager
     */
    private $manager;

    function __construct(Session $session, AuthorizationService $authorizationService, EntityManager $manager)
    {
        $this->session = $session;
        $this->authorizationService = $authorizationService;
        $this->manager = $manager;
    }

    public function acceptCookies()
    {
        $this->session->set('acceptedCookies', 1);

        if($this->authorizationService->isAuthorized())
        {
            $user = $this->authorizationService->getAuthorizedUserOrThrowException();
            $user->setAgreedToCookie(true);
            $user->setAgreedToCookieDateTime(new \DateTime());
            $this->manager->flush();
        }
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     * @return string
     */
    public function getUniqueCode()
    {
        return 'cookie';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('acceptCookies');
    }
}