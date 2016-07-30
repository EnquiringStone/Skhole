<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 30-Jul-16
 * Time: 16:51
 */

namespace AppBundle\Service\Ajax\Teach;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SharedReportsAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    function __construct(EntityManager $manager, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->authorizationService = $authorizationService;
    }

    public function acceptMentorRequest($args)
    {
        $sharedReport = $this->getValidSharedReportById($args['id']);

        if($sharedReport->getHasAccepted()) return; //Already accepted so we don't have to do shit

        $sharedReport->setHasAccepted(true);

        $this->manager->flush();
    }

    public function declineMentorRequest($args)
    {
        $sharedReport = $this->getValidSharedReportById($args['id']);

        if($sharedReport->getHasAccepted()) throw new FrontEndException('course.shared.reports.decline.already.accepted', 'ajaxerrors');

        $this->manager->remove($sharedReport);
        $this->manager->flush();
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     * @return string
     */
    public function getUniqueCode()
    {
        //Shared Reports Ajax Service 1
        return 'SRAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('acceptMentorRequest', 'declineMentorRequest');
    }

    private function getValidSharedReportById($id)
    {
        $sharedReport = $this->manager->getRepository('AppBundle:Report\SharedReports')->find($id);
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        if($sharedReport == null || $sharedReport->getMentorUserId() != $user->getId()) throw new AccessDeniedException();

        return $sharedReport;
    }
}