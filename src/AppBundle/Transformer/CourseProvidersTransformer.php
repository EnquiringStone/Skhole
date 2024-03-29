<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 20-Mar-16
 * Time: 17:12
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class CourseProvidersTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
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
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach($entities as $entity)
            {
                $html.= $this->environment->render(':ajax/create-courses/provider:card.provider.row.html.twig', array('provider' => $entity, 'index' => $entity->getId()));
                $html .= $this->environment->render(':modal/create-course:card.remove.provider.modal.html.twig', array('provider' => $entity, 'modalId' => 'removeProviderModal'.$entity->getId()));
                $html .= $this->environment->render(':modal/create-course:card.add.provider.modal.html.twig', array('provider' => $entity, 'modalId' => 'addProviderModal'.$entity->getId()));
            }
        }
        return $html;
    }

    /**
     * Returns the name. Should be the same as the class name
     *
     * @return string
     */
    function getName()
    {
        return 'CourseProvidersTransformer';
    }
}