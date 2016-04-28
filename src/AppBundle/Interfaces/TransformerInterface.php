<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 22:38
 */

namespace AppBundle\Interfaces;


interface TransformerInterface
{
    /**
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context);

    /**
     * Returns the name. Should be the same as the class name
     * @return string
     */
    function getName();
}