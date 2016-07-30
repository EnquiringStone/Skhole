<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 30-Jul-16
 * Time: 15:43
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use Doctrine\ORM\EntityRepository;

class SharedReportsRepository extends EntityRepository implements PageControlsInterface
{
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

    function getRecords($searchValues, $offset, $limit, $sort, $userId = 0, $sessionId = '')
    {
        $searchValues = $this->replaceSearchValues($searchValues);
        $sort = $this->replaceSort($sort);
        $sort = array($sort['sortAttribute'] => $sort['sortValue']);

        $criteria = array_merge($searchValues, array('mentorUserId' => $userId));
        $resultSet = $this->findBy($criteria, $sort, $limit, $offset);

        return $this->createReturnValues($resultSet, $this->getCountByCriteria($criteria));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0, $sessionId = '')
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId, $sessionId);
    }

    private function replaceSort($sort)
    {
        if($sort == null)
        {
            $sort = array('sortAttribute' => 'id', 'sortValue' => 'DESC');
        }
        return $sort;
    }

    private function replaceSearchValues($searchValues)
    {
        return $searchValues == null ? array() : $searchValues;
    }

    private function createReturnValues($entities, $total)
    {
        return array('resultSet' => $entities, 'total' => $total);
    }
}