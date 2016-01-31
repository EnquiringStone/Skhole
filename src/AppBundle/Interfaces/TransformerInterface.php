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
     * Flattens the entity object to an array.
     * @param $entities
     * @return mixed
     */
    function transformToAjaxResponse($entities);

    /**
     * Returns the name. Should be the same as the class name
     * @return string
     */
    function getName();
}