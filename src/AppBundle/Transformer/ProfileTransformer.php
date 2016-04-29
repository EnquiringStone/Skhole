<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 02:51
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class ProfileTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $environment;

    function __construct(\Twig_Environment $environment)
    {

        $this->environment = $environment;
    }

    /**
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     *
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
                $html .= $this->environment->render(':ajax/profile:profile.details.buttons.html.twig', array('user' => $entity));
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
        return 'ProfileTransformer';
    }
}