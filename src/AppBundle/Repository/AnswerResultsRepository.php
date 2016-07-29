<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 28-Jul-16
 * Time: 22:53
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class AnswerResultsRepository extends EntityRepository
{
    public function getAllAnsweredQuestionByCoursePage($reportId, $pageId)
    {
        return $this->createQueryBuilder('ar')
            ->select('ar')
            ->innerJoin('ar.question', 'q')
            ->innerJoin('q.coursePage', 'cp')
            ->innerJoin('ar.report', 'r')
            ->where('cp.id = :pageId')
            ->andWhere('r.id = :reportId')
            ->setParameter('pageId', $pageId)
            ->setParameter('reportId', $reportId)
            ->getQuery()->getResult();
    }
}