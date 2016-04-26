<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 17:32
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CoursePagesRepository extends EntityRepository
{
    public function getQuestionHighestOrder($pageId)
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.questions', 'q')
            ->select('MAX(q.questionOrder) as highestCount')
            ->where('a.id = :pageId')
            ->setParameter('pageId', $pageId);

        $result = $query->getQuery()->execute()[0];

        return intval($result['highestCount']);
    }
}