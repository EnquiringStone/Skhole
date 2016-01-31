<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Jan-16
 * Time: 21:40
 */

namespace AppBundle\Interfaces;


interface SortableInterface
{
    /**
     * Sorts the given attribute and returns a number of entities,
     * started at the offset
     * @param $attribute
     * @param $order
     * @param $offset
     * @param $userId
     * @param $limit
     * @return $entities
     */
    function sortByAttribute($attribute, $order, $offset, $limit, $userId);

    /**
     * Gets all sortable attributes for the entity
     * @return array
     */
    function getSortableAttributes();
}