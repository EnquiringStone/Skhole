<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 06-Feb-16
 * Time: 15:42
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use AppBundle\Interfaces\PaginationInterface;
use AppBundle\Interfaces\SearchableInterface;
use AppBundle\Interfaces\SortableInterface;
use AppBundle\Util\SecurityHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class CoursesRepository extends EntityRepository implements PageControlsInterface
{
    public function getHighestOrder($courseId)
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.coursePages', 'p')
            ->select('MAX(p.pageOrder) as highestCount')
            ->where('a.id = :courseId')
            ->setParameter('courseId', $courseId);

        $result = $query->getQuery()->execute()[0];

        return intval($result['highestCount']);
    }

    public function getCountByUserId($userId)
    {
        return $this->getCountByCriteria(array('userInsertedId' => $userId));
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
    public function hasPagination()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasSearch()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasSort()
    {
        return true;
    }

    public function getRecords($searchValues, $offset, $limit, $sort, $userId = 0)
    {
        $searchValues = $this->replaceSearchValues($searchValues);
        $sort = $this->replaceSort($sort);
        $sort = array($sort['sortAttribute'] => $sort['sortValue']);
        if($userId > 0)
        {
            $criteria = array_merge($searchValues, array('userInsertedId' => $userId, 'removed' => false));
            $resultSet = $this->findBy($criteria, $sort, $limit, $offset);
            $maxEntities = $this->getCountByCriteria($criteria);
        }
        else
        {
            $state = $this->getEntityManager()->getRepository('AppBundle:Course\CourseStates');
            $stateId = $state->findOneBy(array('state' => 'Finished'));

            $criteria = array('stateId' => $stateId->getId(), 'isUndesirable' => false, 'removed' => false);

            $resultSet = $this->findBy($criteria, $sort, $limit, $offset);
            $maxEntities = $this->getCountByCriteria($criteria);
        }
        return $this->createReturnValues($resultSet, $maxEntities);
    }

    public function getRecordsBySearch($offset, $limit, $sort, $searchAttributes, $userId = 0)
    {
        $sort = $this->replaceSort($sort);

        $qb = $this->createQueryBuilder('courses');
        $qb->select('distinct courses');
        $qb->leftJoin('courses.courseCard', 'courseCard');
        $qb->leftJoin('courseCard.teachers', 'teachers');
        $qb->leftJoin('courseCard.providers', 'providers');

        $i = 0;
        foreach($searchAttributes as $entity => $attribute)
        {
            foreach($attribute as $attributeName => $attributeValue)
            {
                $qb->orWhere($qb->expr()->like($entity.'.'.$attributeName, '?'.$i));
                $qb->setParameter($i, '%'.$attributeValue.'%');
            }
        }
        if($userId > 0)
            $qb->andWhere('courses.userInsertedId = '.$userId);

        $qb->orderBy('courses.'.$sort['sortAttribute'], $sort['sortValue']);
        $resultSet = $qb->getQuery()->getResult();
        $total = count($resultSet);
        $collection = new ArrayCollection($resultSet);

        return $this->createReturnValues($collection->slice($offset, $limit), $total);
    }

    private function replaceSort($sort)
    {
        if($sort == null)
        {
            $sort = array('sortAttribute' => 'id', 'sortValue' => 'ASC');
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