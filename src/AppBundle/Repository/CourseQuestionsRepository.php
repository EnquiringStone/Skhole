<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 17:33
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CourseQuestionsRepository extends EntityRepository
{
    public function getAnswerHighestOrder($questionId)
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.courseAnswers', 'ca')
            ->select('MAX(ca.answerOrder) as highestCount')
            ->where('a.id = :questionId')
            ->setParameter('questionId', $questionId);

        $result = $query->getQuery()->execute()[0];

        return intval($result['highestCount']);
    }
}