<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-Feb-16
 * Time: 14:55
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use AppBundle\Interfaces\PaginationInterface;
use Doctrine\ORM\EntityRepository;

class CourseReviewsRepository extends EntityRepository implements PageControlsInterface
{
    public function getCountByCourseId($courseId)
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.courseId = :courseId')
            ->setParameter('courseId', $courseId);

        return $query->getQuery()->getSingleScalarResult();
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
     * Gets all entities for the given offset and limit belonging to the userId
     *
     * @param $offset int
     * @param $limit int
     * @param $attribute string
     * @param $order string
     * @param $userId int
     * @return mixed
     * @throws \Exception
     */
    public function getByPage($offset, $limit, $attribute, $order, $userId)
    {
        //We don't need to use sort here (attribute and order)
        return $this->findBy(array('userInsertedId' => $userId), null, $limit, $offset);
    }

    /**
     * Gets the count of entities
     *
     * @return mixed
     */
    public function getPaginationCount()
    {
        return $this->getCountByCourseId(func_get_args()[0]);
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

    function getRecords($searchValues, $offset, $limit, $sort, $userId = 0, $sessionId = '')
    {
        if($userId > 0) $search = array_merge($searchValues, array('userInsertedId' => $userId));
        else $search = $searchValues;

        return array('resultSet' => $this->findBy($search, null, $limit, $offset),
            'total' => $this->getCountByCriteria($search));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0, $sessionId = '')
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId);
    }
}