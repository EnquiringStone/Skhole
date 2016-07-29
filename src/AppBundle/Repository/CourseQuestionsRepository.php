<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 17:33
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Report\Reports;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

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

    /**
     * @param Reports $report
     * @param User $user
     * @param null $sessionId
     * @return array
     */
    public function getMissingQuestions(Reports $report, User $user = null, $sessionId = null)
    {
        $query = $this->createQueryBuilder('cq')
            ->select('cq.id')
            ->innerJoin('cq.coursePage', 'cp')
            ->innerJoin('cp.course', 'c')
            ->innerJoin('AppBundle\Entity\Report\Reports', 'r', Join::WITH, 'r.courseId = c.id')
            ->leftJoin('AppBundle\Entity\Report\AnswerResults', 'ar', Join::WITH, 'ar.questionId = cq.id')
            ->where('c.id = :courseId')
            ->andWhere('ar.questionId is NULL')
            ->andWhere('ar.reportId = :reportId')
            ->setParameter('courseId', $report->getCourseId())
            ->setParameter('reportId', $report->getId());

        if($user == null)
        {
            $query->andWhere('r.sessionId = :sessionId')
                ->setParameter('sessionId', $sessionId);
        }
        else
        {
            $query->andWhere('r.userId = :userId')
                ->setParameter('userId', $user->getId());
        }
        return $query->getQuery()->getResult();
    }

    public function getAllQuestionsByCourse($courseId)
    {
        return $this->createQueryBuilder('cq')
            ->select('cq')
            ->innerJoin('cq.coursePage', 'cp')
            ->innerJoin('cp.course', 'c')
            ->where('c.id = :courseId')
            ->setParameter('courseId', $courseId)
            ->getQuery()->getResult();
    }

    public function getAllAnsweredQuestionsByCourseAndReport($courseId, $reportId)
    {
        return $this->createQueryBuilder('cq')
            ->select('cq')
            ->innerJoin('cq.coursePage', 'cp')
            ->innerJoin('cp.course', 'c')
            ->innerJoin('AppBundle\Entity\Report\AnswerResults', 'ar', Join::WITH, 'ar.questionId = cq.id')
            ->where('c.id = :courseId')
            ->andWhere('ar.reportId = :reportId')
            ->setParameter('courseId', $courseId)
            ->setParameter('reportId', $reportId)
            ->getQuery()->getResult();
    }

    public function GetAllUnansweredQuestionsByCourseAndReport($courseId, $reportId)
    {
        $allQuestions = $this->getAllQuestionsByCourse($courseId);
        $answeredQuestions = $this->getAllAnsweredQuestionsByCourseAndReport($courseId, $reportId);

        $unansweredQuestions = array();

        foreach ($allQuestions as $question)
        {
            if(!in_array($question, $answeredQuestions))
                $unansweredQuestions[] = $question;
        }

        return $unansweredQuestions;
    }
}