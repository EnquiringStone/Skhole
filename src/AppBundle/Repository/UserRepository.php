<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 31-Jul-16
 * Time: 14:10
 */

namespace AppBundle\Repository;


use AppBundle\Interfaces\PageControlsInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements PageControlsInterface
{
    public function getAllUsersByMentor($mentorId, $offset, $limit, $sortAttribute, $sortValue)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->innerJoin('user.sharedReports', 'report')
            ->where('report.hasAccepted = 1')
            ->andWhere('report.mentorUserId = :mentorId')
            ->setParameter('mentorId', $mentorId)
            ->orderBy('user.'.$sortAttribute, $sortValue)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function getUserCountByMentor($mentorId)
    {
        return sizeof($this->createQueryBuilder('user')
            ->innerJoin('user.sharedReports', 'report')
            ->where('report.hasAccepted = 1')
            ->andWhere('report.mentorUserId = :mentorId')
            ->setParameter('mentorId', $mentorId)
            ->select('user')
            ->getQuery()->getResult());
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
        $sort = $this->replaceSort($sort);

        return $this->createReturnValues($this->getAllUsersByMentor($userId, $offset, $limit, $sort['sortAttribute'], $sort['sortValue']), $this->getUserCountByMentor($userId));
    }

    function getRecordsBySearch($offset, $limit, $sort, $searchParams, $userId = 0, $sessionId = '')
    {
        return $this->getRecords($searchParams['defaultSearch'], $offset, $limit, $sort, $userId, $sessionId);
    }

    private function replaceSort($sort)
    {
        if($sort == null)
        {
            $sort = array('sortAttribute' => 'firstName', 'sortValue' => 'ASC');
        }
        return $sort;
    }

    private function createReturnValues($entities, $total)
    {
        return array('resultSet' => $entities, 'total' => $total);
    }
}