<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 06-Feb-16
 * Time: 15:42
 */

namespace AppBundle\Repository;


use AppBundle\Doctrine\SearchQuery;
use AppBundle\Interfaces\PageControlsInterface;
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

    public function getRecords($searchValues, $offset, $limit, $sort, $userId = 0, $sessionId = '')
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

    public function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0, $sessionId = '')
    {
        $sort = $this->replaceSort($sort);
        $searchQuery = new SearchQuery($searchParams['searchQuery'], $searchParams['correlationType'], $offset, $limit, $sort);

        $qb = $this->createQueryBuilder('courses');
        $qb->select('distinct courses');
        $qb->leftJoin('courses.courseCard', 'courseCard');
        $qb->leftJoin('courseCard.teachers', 'teachers');
        $qb->leftJoin('courseCard.providers', 'providers');
        $qb->leftJoin('courses.tags', 'tags');
        $qb->leftJoin('courses.language', 'languages');
        $qb->leftJoin('courses.level', 'levels');

        if($userId > 0)
            $qb->andWhere('courses.userInsertedId = '.$userId);
        else
        {
            $qb->andWhere('courses.stateId = 2');
            $qb->andWhere('courses.isUndesirable = false');
        }
        $qb->andWhere('courses.removed = false');

        $index = 0;
        foreach($searchParams['defaultSearch'] as $attribute => $value)
        {
            $qb->andWhere('courses.'.$attribute, '?'.$index);
            $qb->setParameter($index, $value);
            $index ++;
        }

        $qb = $searchQuery->buildFromQuery($qb);
        return $searchQuery->getResult($qb, 'courses');
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