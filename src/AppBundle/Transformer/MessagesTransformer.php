<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 22:35
 */

namespace AppBundle\Transformer;


use AppBundle\Entity\Messages;
use AppBundle\Interfaces\TransformerInterface;
use Twig_Extensions_Extension_Date;

class MessagesTransformer implements TransformerInterface
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
    public function transformToAjaxResponse($entities, $context)
    {
        return $this->environment->render(':ajax/messages:messages.table.html.twig', array('messages' => $entities));
    }

    /**
     * Returns the name. Should be the same as the class name
     *
     * @return string
     */
    function getName()
    {
        return 'MessagesTransformer';
    }
}