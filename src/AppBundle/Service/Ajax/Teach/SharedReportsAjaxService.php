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
    /**
     * @var \Twig_Environment
     */
    private $environment;

    function __construct(EntityManager $manager, AuthorizationService $authorizationService, \Twig_Environment $environment)
    {
        $this->manager = $manager;
        $this->authorizationService = $authorizationService;
        $this->environment = $environment;
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

    public function getAllReports($args)
    {
        $mentor = $this->authorizationService->getAuthorizedUserOrThrowException();
        $user = $this->manager->getRepository('AppBundle:User')->find($args['userId']);
        if($user == null) throw new AccessDeniedException();

        $reports = $this->manager->getRepository('AppBundle:Report\SharedReports')->findBy(array('userId' => $user->getId(), 'mentorUserId' => $mentor->getId(), 'hasAccepted' => true));
        if(sizeof($reports) <= 0) throw new FrontEndException('course.shared.reports.nothing.found', 'ajaxerrors');

        $html = $this->environment->render(':ajax/teach:shared.reports.table.html.twig', array('reports' => $reports));

        return array('html' => $html);
    }

    public function saveReportChanges($args)
    {
        $report = $this->getValidSharedReportById($args['reportId']);

        if(array_key_exists('rating', $args) && $args['rating'] != '')
        {
            if(intval($args['rating']) < 0 || intval($args['rating'] > 5)) throw new FrontEndException('course.reviews.invalid.rating', 'ajaxerrors');
            $report->setRating($args['rating']);
        }

        if(array_key_exists('hasRevised', $args))
        {
            $report->setHasRevised($args['hasRevised'] == 'true');
        }
        $this->manager->flush();
    }

    public function removeReport($args)
    {
        $report = $this->getValidSharedReportById($args['id']);
        $user = $report->getUser();
        $mentor = $this->authorizationService->getAuthorizedUserOrThrowException();

        $returnValues = array('lastRow' => 'no');

        if($user->getStatisticsByMentor($mentor->getId())['total'] == 1)
        {
            $returnValues['lastRow'] = 'yes';
            $returnValues['userId'] = $user->getId();
        }

        $this->manager->remove($report);
        $this->manager->flush();

        return $returnValues;
    }

    public function removeAllReports($args)
    {
        $mentor = $this->authorizationService->getAuthorizedUserOrThrowException();
        $user = $this->manager->getRepository('AppBundle:User')->find($args['id']);
        if($user == null) throw new AccessDeniedException();

        $reports = $this->manager->getRepository('AppBundle:Report\SharedReports')->findBy(array('mentorUserId' => $mentor->getId(), 'userId' => $user->getId(), 'hasAccepted' => true));
        foreach ($reports as $report)
        {
            $this->manager->remove($report);
        }
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
        return array('acceptMentorRequest', 'declineMentorRequest', 'getAllReports', 'saveReportChanges', 'removeReport', 'removeAllReports');
    }

    private function getValidSharedReportById($id)
    {
        $sharedReport = $this->manager->getRepository('AppBundle:Report\SharedReports')->find($id);
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        if($sharedReport == null || $sharedReport->getMentorUserId() != $user->getId()) throw new AccessDeniedException();

        return $sharedReport;
    }
}