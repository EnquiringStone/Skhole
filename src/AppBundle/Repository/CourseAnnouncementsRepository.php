<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-May-16
 * Time: 19:12
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use Doctrine\ORM\EntityRepository;

class CourseAnnouncementsRepository extends EntityRepository implements PageControlsInterface
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
        return false;
    }

    function getRecords($searchValues, $offset, $limit, $sort, $userId = 0)
    {
        $searchValues = $this->replaceSearchValues($searchValues);

        if($userId > 0) $search = array_merge($searchValues, array('userInsertedId' => $userId));
        else $search = $searchValues;

        return array('resultSet' => $this->findBy($search, null, $limit, $offset), 'total' => $this->getCountByCriteria($search));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0)
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId);
    }

    private function replaceSearchValues($searchValues)
    {
        return $searchValues == null ? array() : $searchValues;
    }
}