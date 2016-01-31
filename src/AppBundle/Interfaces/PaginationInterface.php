<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Jan-16
 * Time: 00:11
 */

namespace AppBundle\Interfaces;


interface PaginationInterface
{
    /**
     * Gets all entities for the given offset and limit belonging to the userId
     *
     * @param $offset int
     * @param $userId int
     * @param $limit int
     * @param $attribute string
     * @param $order string
     * @return mixed
     */
    function getByPage($offset, $limit, $attribute, $order, $userId);

    /**
     * Gets the count of entities
     * @return mixed
     */
    function getPaginationCount();
}