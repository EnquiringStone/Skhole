<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 23:45
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Messages;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Util\PageControlHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessagesAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    public function __construct(EntityManager $manager, \Twig_Environment $environment, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->environment = $environment;
        $this->authorizationService = $authorizationService;
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

    public function getMessageModals($args)
    {
        $this->validate($args);

        if(!array_key_exists('defaultValues', $args)) $args['defaultValues'] = array();
        else $args['defaultValues'] = PageControlHelper::createDefaultSearch($args['defaultValues']);

        $repo = $this->manager->getRepository('AppBundle:Messages');

        $resultSet = $repo->getRecords($args['defaultValues'], $args['offset'], $args['limit'], array('sortAttribute' => $args['sortAttribute'], 'sortValue' => $args['sortValue']),
            $args['context'] == 'SELF' ? $this->authorizationService->getAuthorizedUserOrThrowException()->getId() : 0);

        $html = '';
        $index = 1;
        foreach($resultSet['resultSet'] as $result)
        {
            $html .= $this->environment->render(':modal/dashboard:messages.modal.html.twig', array(
                'message' => $result,
                'modalId' => 'messagesModal'.$index
            ));
            $index ++;
        }
        return array('html' => $html);
    }

    public function getUnreadMessagesCount()
    {
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        $messages = $this->manager->getRepository('AppBundle:Messages')->findBy(array('isRead' => false, 'userId' => $user->getId()));

        return array('unreadMessages' => sizeof($messages));
    }

    private function validate($args)
    {
        if(!array_key_exists('offset', $args) || !array_key_exists('limit', $args) || !array_key_exists('sortAttribute', $args) || !array_key_exists('sortValue', $args))
            throw new \Exception('Offset, limit, sort attribute and sort value needs to be specified');

        if($args['context'] == 'SELF' && !$this->authorizationService->isAuthorized())
            throw new \Exception('context is self but no user context found');
    }

    private function hasRights($entity)
    {
        if(!$this->authorizationService->isAuthorized())
            throw new AccessDeniedException('Nope');
        if($entity instanceof Messages)
        {
            return $this->authorizationService->getAuthorizedUserOrThrowException()->getId() == $entity->getUserId();
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
            'readById', 'getMessageModals', 'getUnreadMessagesCount');
    }
}