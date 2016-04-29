<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 16:58
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class CourseCollectionItemsRepository extends EntityRepository implements PageControlsInterface
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

    public function courseIsInCollection($userId, $courseId)
    {
        return $this->findOneBy(array('userId' => $userId, 'courseId' => $courseId)) != null;
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
        $sort = $this->replaceSort($sort);
        $qb = $this->createQueryBuilder('collection');
        $qb->leftJoin('collection.course', 'course');

        $qb->andWhere('collection.userId = ?1');
        $qb->setParameter(1, $userId);

        $index = 2;
        foreach($searchValues as $attribute => $value)
        {
            $qb->andWhere('collection.'.$attribute.' = ?'.$index);
            $qb->setParameter($index, $value);
            $index ++;
        }
        $entity = $sort['sortAttribute'] == 'insertDateTime' ? 'collection' : 'course';

        $qb->orderBy($entity.'.'.$sort['sortAttribute'], $sort['sortValue']);
        $result = $qb->getQuery()->getResult();
        $resultSet = new ArrayCollection($result);

        return array('resultSet' => $resultSet->slice($offset, $limit), 'total' => count($result));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0)
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId);
    }

    private function createReturnValues($entities, $total)
    {
        return array('resultSet' => $entities, 'total' => $total);
    }

    private function replaceSort($sort)
    {
        if($sort == null)
        {
            $sort = array('sortAttribute' => 'insertDateTime', 'sortValue' => 'DESC');
        }
        return $sort;
    }
}