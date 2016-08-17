<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 31-Jul-16
 * Time: 15:00
 */

namespace AppBundle\Transformer;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class UserTransformer implements TransformerInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    /**
     * UserTransformer constructor.
     * @param \Twig_Environment $environment
     * @param AuthorizationService $authorizationService
     */
    function __construct(\Twig_Environment $environment, AuthorizationService $authorizationService)
    {
        $this->environment = $environment;
        $this->authorizationService = $authorizationService;
    }

    /**
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        $html = '';
        $index = 1;
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach ($entities as $entity)
            {
                $html .= $this->environment->render(':ajax/teach:mentor.user.row.html.twig', array('user' => $entity, 'index' => $index, 'mentorId' => $this->authorizationService->getAuthorizedUserOrThrowException()->getId()));
                $html .= $this->environment->render(':modal/teach:education.profile.modal.html.twig', array('user' => $entity, 'modalId' => 'educationProfileModal'.$index));
                $html .= $this->environment->render(':modal/teach:remove.all.reports.modal.html.twig', array('user' => $entity, 'modalId' => 'removeAllReportsModal'.$index));
                $index ++;
            }
        }
        return $html;
    }

    /**
     * Returns the name. Should be the same as the class name
     * @return string
     */
    function getName()
    {
        return 'UserTransformer';
    }
}