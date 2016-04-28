<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Jan-16
 * Time: 21:39
 */

namespace AppBundle\Repository;

use AppBundle\Interfaces\PageControlsInterface;
use AppBundle\Interfaces\PaginationInterface;
use AppBundle\Interfaces\SortableInterface;
use Doctrine\ORM\EntityRepository;

class MessagesRepository extends EntityRepository implements PageControlsInterface
{
    public function getCountByUserId($userId)
    {
        return $this->getCountByCriteria(array('userId' => $userId));
    }

    public function getCountByCriteria($criteria)
    {
        $query = $this->createQueryBuilder('c');
        $query->select('COUNT(c)');
        $i = 0;
        foreach($criteria as $key => $value)
        {
            $query->andWhere('c.'.$key.' = ?'.$i);
            $query->setParameter($i, $value);
            $i++;
        }
        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @return bool
     */
    function hasPagination()
    {
        return true;
    }

    /**
     * @return bool
     */
    function hasSearch()
    {
        return false;
    }

    /**
     * @return bool
     */
    function hasSort()
    {
        return true;
    }

    function getRecords($searchValues, $offset, $limit, $sort, $userId = 0)
    {
        $sort = array($sort['sortAttribute'] => $sort['sortValue']);

        if($userId > 0) $search = array_merge($searchValues, array('userId' => $userId));
        else $search = $searchValues;

        return array('resultSet' => $this->findBy($search, $sort, $limit, $offset), 'total' => $this->getCountByCriteria($search));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0)
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId);
    }
}