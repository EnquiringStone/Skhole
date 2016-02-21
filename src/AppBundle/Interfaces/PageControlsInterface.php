<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 13-Feb-16
 * Time: 19:07
 */

namespace AppBundle\Interfaces;


interface PageControlsInterface
{
    /**
     * @return bool
     */
    function hasPagination();

    /**
     * @return bool
     */
    function hasSearch();

    /**
     * @return bool
     */
    function hasSort();

    function getRecords($searchValues, $offset, $limit, $sort, $userId = 0);

    function getRecordsBySearch($offset, $limit, $sort, $searchAttributes, $userId = 0);
}