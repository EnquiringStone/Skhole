<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 14-May-16
 * Time: 10:10
 */

namespace AppBundle\Service\Ajax\Learn;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Course\CourseAnswers;
use AppBundle\Entity\Course\CoursePages;
use AppBundle\Entity\Course\CourseQuestions;
use AppBundle\Entity\Course\CourseReviews;
use AppBundle\Entity\Course\Courses;
use AppBundle\Entity\Progress\CoursePageProgressions;
use AppBundle\Entity\Progress\CourseProgressions;
use AppBundle\Entity\Report\AnswerResults;
use AppBundle\Entity\Report\MultipleChoiceAnswers;
use AppBundle\Entity\Report\Reports;
use AppBundle\Entity\Report\SharedReports;
use AppBundle\Entity\User;
use AppBundle\Enum\CoursePageTypeEnum;
use AppBundle\Enum\PageTypeEnum;
use AppBundle\Exception\CourseRemovedException;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StudyAjaxService implements AjaxInterface
{
    /**
     * @var AuthorizationService
     */
    private $authorizationService;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(AuthorizationService $authorizationService, EntityManager $manager, Session $session, \Twig_Environment $environment)
    {
        $this->authorizationService = $authorizationService;
        $this->manager = $manager;
        $this->session = $session;
        $this->environment = $environment;
    }

    public function saveStatistics($args)
    {
        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['courseId']);

        $this->isSpecifiedCourseValid($course);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->findOneBy(array('pageOrder' => $args['order'], 'courseId' => $args['courseId']));
        if($page == null) throw new EntityNotFoundException();

        $courseProgression = $this->createOrFindCourseProgression($course);

        $pageProgression = $this->createOrFindPageProgression($courseProgression, $page);

        if(!CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::ExerciseType, $page->getPageType()->getType()))
        {
            $pageProgression->setIsFinished(true);
            $pageProgression->setFinishedDateTime(new \DateTime());
            $this->manager->flush();
        }
        $this->isCourseComplete($courseProgression, $course);
    }

    public function saveAnswers($args)
    {
        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['courseId']);
        $this->isSpecifiedCourseValid($course);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->find($args['pageId']);

        if($page == null) throw new EntityNotFoundException();

        if(!PageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::ExerciseType, $page->getPageType()->getType()) || $page->getCourseId() != $course->getId()
            || $page->getQuestions()->isEmpty())
            throw new AccessDeniedException();

        $report = $this->createOrFindReport($course);

        if(array_key_exists('multipleChoice', $args))
        {
            foreach ($args['multipleChoice'] as $questionId => $answers)
            {
                if(!is_array($answers)) throw new AccessDeniedException();

                $question = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->find($questionId);
                if($question == null || $question->getCoursePageId() != $page->getId()) throw new AccessDeniedException();

                $answerResult = $this->createOrFindAnswerResult($report, $question);

                if(!$answerResult->getMultipleChoiceAnswers()->isEmpty())
                {
                    foreach ($answerResult->getMultipleChoiceAnswers()->toArray() as $ar)
                    {
                        $this->manager->remove($ar);
                    }
                    $answerResult->getMultipleChoiceAnswers()->clear();
                    $this->manager->flush();
                }

                foreach ($answers as $answerId)
                {
                    $answer = $this->manager->getRepository('AppBundle:Course\CourseAnswers')->find($answerId);
                    if($answers == null || $answer->getCourseQuestionId() != $question->getId()) throw new AccessDeniedException();

                    $this->createMultipleChoiceAnswer($answerResult, $answer);
                }
                $this->manager->flush();
            }
        }
        if(array_key_exists('openQuestion', $args))
        {
            foreach ($args['openQuestion'] as $questionId => $answer)
            {
                if(!is_string($answer)) throw new AccessDeniedException();

                $question = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->find($questionId);
                if($question == null || $question->getCoursePageId() != $page->getId()) throw new AccessDeniedException();

                $answerResult = $this->createOrFindAnswerResult($report, $question);

                $answerResult->setAnswer($answer);
            }
            $this->manager->flush();
        }


        $this->updateCourseReportToCompleteIfNeeded($report);
    }

    public function addQuickCourseReview($args)
    {
        if($this->authorizationService->isAuthorized())
        {
            $this->addCourseReview($args);
            return;
        }
        $this->contentRatingIsValid($args);

        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        $this->isSpecifiedCourseValid($course);

        $review = $this->manager->getRepository('AppBundle:Course\CourseReviews')->findOneBy(array('sessionId' => $this->session->getId(), 'courseId' => $args['id']));
        if($review != null) throw new FrontEndException('course.reviews.one.per.user', 'ajaxerrors');

        $review = new CourseReviews();
        $review->setSessionId($this->session->getId());
        $review->setInsertDateTime(new \DateTime());
        $review->setContentRating($args['rating']);
        $review->setIsUndesirable(false);
        $review->setCourse($course);

        $this->manager->persist($review);
        $this->manager->flush();
    }

    public function addCourseReview($args)
    {
        $this->contentRatingIsValid($args);

        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        $this->isSpecifiedCourseValid($course);

        $review = $this->manager->getRepository('AppBundle:Course\CourseReviews')->findOneBy(array('userInsertedId' => $user->getId(), 'courseId' => $course->getId()));
        if($review != null) throw new FrontEndException('course.reviews.one.per.user', 'ajaxerrors');

        $review = new CourseReviews();
        $review->setInsertUser($user);
        $review->setIsUndesirable(false);
        $review->setContentRating($args['rating']);
        $review->setInsertDateTime(new \DateTime());
        $review->setCourse($course);
        if(array_key_exists('remarks', $args)) $review->setRemarks($args['remarks']);

        $this->manager->persist($review);
        $this->manager->flush();
    }

    public function updateCourseReview($args)
    {
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        $this->isSpecifiedCourseValid($course);

        $review = $this->manager->getRepository('AppBundle:Course\CourseReviews')->findOneBy(array('userInsertedId' => $user->getId(), 'courseId' => $course->getId()));
        if($review == null) throw new AccessDeniedException();

        $review->setRemarks($args['remarks']);

        $this->manager->flush();
    }

    public function validateReport($args)
    {
        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['courseId']);
        $this->isSpecifiedCourseValid($course);

        $criteria = array('courseId' => $course->getId());
        if($this->authorizationService->isAuthorized())
            $criteria = array_merge($criteria, array('userId' => $this->authorizationService->getAuthorizedUserOrThrowException()->getId()));
        else
            $criteria = array_merge($criteria, array('sessionId' => $this->session->getId()));

        $report = $this->manager->getRepository('AppBundle:Report\Reports')->findOneBy($criteria);

        if($report == null) throw new FrontEndException('course.reports.view.not.complete', 'ajaxerrors', array('%1' => ''));

        if(!$report->getIsComplete())
        {
            $unansweredQuestions = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->GetAllUnansweredQuestionsByCourseAndReport($course->getId(), $report->getId());
            if(sizeof($unansweredQuestions)  <= 0)
            {
                throw new FrontEndException('course.reports.view.not.complete', 'ajaxerrors', array('%1' => ''));
            }
            $friendlyArray = array();
            foreach ($unansweredQuestions as $question)
            {
                $friendlyArray[$question->getCoursePageId()][] = $question;
            }

            $html = $this->environment->render(':errors:learn.report.not.complete.error.message.html.twig', array('unansweredQuestions' => $friendlyArray, 'course' => $course));
            throw new FrontEndException('course.reports.view.not.complete', 'ajaxerrors', array('%1' => $html));
        }

    }

    public function removeReport($args)
    {
        $report = $this->manager->getRepository('AppBundle:Report\Reports')->find($args['reportId']);

        $this->validateSpecifiedReport($report);
        if($this->authorizationService->isAuthorized())
            if(!$report->getSharedReports()->isEmpty()) throw new FrontEndException('course.reports.shared.remove', 'ajaxerrors', array('%1' => $report->getSharedReports()->count()));

        $this->manager->remove($report);
        $this->manager->flush();
    }

    public function shareReport($args)
    {
        if(!$this->authorizationService->isAuthorized()) throw new FrontEndException('course.reports.share.not.logged.in', 'ajaxerrors');
        $user = $this->authorizationService->getAuthorizedUserOrThrowException();

        $report = $this->manager->getRepository('AppBundle:Report\Reports')->find($args['reportId']);
        $this->validateSpecifiedReport($report);

        $mentor = $this->manager->getRepository('AppBundle:User')->findOneBy(array('mentorCode' => $args['mentorCode']));
        if($mentor == null) return; //Silent error. As we do not want the user to map the mentor code to people

        if($mentor->getId() == $user->getId()) throw new FrontEndException('course.reports.share.same', 'ajaxerrors');

        $sharedReport = $this->manager->getRepository('AppBundle:Report\SharedReports')->findOneBy(array('userId' => $user->getId(), 'mentorUserId' => $mentor->getId(), 'reportId' => $report->getId()));
        if($sharedReport != null) throw new FrontEndException('course.reports.already.shared', 'ajaxerrors');

        $sharedReport = new SharedReports();
        $sharedReport->setHasAccepted(false);
        $sharedReport->setHasRevised(false);
        $sharedReport->setInsertDateTime(new \DateTime());
        $sharedReport->setMentor($mentor);
        $sharedReport->setUser($user);
        $sharedReport->setReport($report);

        $this->manager->persist($sharedReport);
        $report->addSharedReport($sharedReport);

        $this->manager->flush();
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Study Ajax Service
        return 'STAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('addQuickCourseReview', 'addCourseReview', 'updateCourseReview', 'saveAnswers', 'validateReport', 'removeReport', 'shareReport');
    }

    private function isSpecifiedCourseValid(Courses $course)
    {
        if($course == null) throw new EntityNotFoundException();

        if($course->getState()->getStateCode() == 'OK' && $course->getIsUndesirable() == false)
            return;

        if($course->getRemoved()) throw new CourseRemovedException();

        throw new AccessDeniedException();
    }

    /**
     * @param Courses $course
     * @param User    $user
     * @return CourseProgressions
     */
    private function createOrFindCourseProgression(Courses $course)
    {
        if($this->authorizationService->isAuthorized())
            $progression = $this->manager->getRepository('AppBundle:Progress\CourseProgressions')->findOneBy(
                array('courseId' => $course->getId(), 'userId' => $this->authorizationService->getAuthorizedUserOrThrowException()->getId()));
        else
            $progression = $this->manager->getRepository('AppBundle:Progress\CourseProgressions')->findOneBy(
                array('courseId' => $course->getId(), 'sessionId' => $this->session->getId()));

        if($progression == null)
        {
            $progression = new CourseProgressions();
            $progression->setCourse($course);
            if($this->authorizationService->isAuthorized())
                $progression->setUser($this->authorizationService->getAuthorizedUserOrThrowException());
            else
                $progression->setSessionId($this->session->getId());
            $progression->setIsFinished(false);
            $progression->setStartDateTime(new \DateTime());
            $this->manager->persist($progression);
            $this->manager->flush();
        }
        return $progression;
    }

    /**
     * @param CourseProgressions $progression
     * @param CoursePages        $page
     * @return CoursePageProgressions
     */
    private function createOrFindPageProgression(CourseProgressions $progression, CoursePages $page)
    {
        $pageProgression = $this->manager->getRepository('AppBundle:Progress\CoursePageProgressions')->findOneBy(array('courseProgressionId' => $progression->getId(), 'pageId' => $page->getId()));
        if($pageProgression == null)
        {
            $pageProgression = new CoursePageProgressions();
            $pageProgression->setCourseProgression($progression);
            $pageProgression->setPage($page);
            $pageProgression->setStartDateTime(new \DateTime());
            $pageProgression->setIsFinished(false);
            $this->manager->persist($pageProgression);
            $this->manager->flush();
        }
        return $pageProgression;
    }

    private function isCourseComplete(CourseProgressions $courseProgression, Courses $course)
    {
        if($courseProgression->getProgressionPages()->count() == $course->getCoursePages()->count())
        {
            $complete = true;
            foreach($courseProgression->getProgressionPages() as $page)
            {
                if($page->getIsFinished() == false) $complete = false;
            }

            if($complete == true)
            {
                $courseProgression->setFinishedDateTime(new \DateTime());
                $courseProgression->setIsFinished(true);
                $this->manager->flush();
            }
        }
    }

    private function contentRatingIsValid($args)
    {
        $contentRating = '';
        if(array_key_exists('rating', $args))
            $contentRating = $args['rating'];
        if(!is_numeric($contentRating) || $contentRating < 0 || $contentRating > 5) throw new FrontEndException('course.reviews.invalid.rating', 'ajaxerrors');
    }

    /**
     * @param Courses $course
     * @return Reports
     * @throws FrontEndException
     */
    private function createOrFindReport(Courses $course)
    {
        $criteria = array('courseId' => $course->getId());
        if($this->authorizationService->isAuthorized())
            $criteria = array_merge($criteria, array('userId' => $this->authorizationService->getAuthorizedUserOrThrowException()->getId()));
        else
            $criteria = array_merge($criteria, array('sessionId' => $this->session->getId()));

        $report = $this->manager->getRepository('AppBundle:Report\Reports')->findOneBy($criteria);

        if($report != null) {
            if($report->getIsComplete() && !$report->getSharedReports()->isEmpty()) throw new FrontEndException('course.reports.completed.and.shared', 'ajaxerrors');
            return $report;
        }

        $report = new Reports();
        $report->setIsComplete(false);
        $report->setCourse($course);
        $report->setCourseId($course->getId()); //This will be automaticly filled. However that mechanic is in this instance too slow. We need the id asap.
        $report->setInsertDateTime(new \DateTime());
        $this->authorizationService->isAuthorized() ? $report->setUser($this->authorizationService->getAuthorizedUserOrThrowException()) : $report->setSessionId($this->session->getId());

        $this->manager->persist($report);
        $this->manager->flush($report);
        return $report;
    }

    /**
     * @param Reports $report
     * @param CourseQuestions $question
     * @return AnswerResults
     */
    private function createOrFindAnswerResult(Reports $report, CourseQuestions $question)
    {
        $answerResult = $this->manager->getRepository('AppBundle:Report\AnswerResults')->findOneBy(array('reportId' => $report->getId(), 'questionId' => $question->getId()));

        if(!$answerResult == null) return $answerResult;

        $answerResult = new AnswerResults();
        $answerResult->setQuestion($question);
        $answerResult->setReport($report);

        $this->manager->persist($answerResult);
        $this->manager->flush();

        return $answerResult;
    }

    /**
     * @param AnswerResults $answerResult
     * @param CourseAnswers $answer
     * @return MultipleChoiceAnswers
     */
    private function createMultipleChoiceAnswer(AnswerResults $answerResult, CourseAnswers $answer)
    {
        $insertAnswer = new MultipleChoiceAnswers();
        $insertAnswer->setAnswer($answer);
        $insertAnswer->setAnswerResult($answerResult);
        $this->manager->persist($insertAnswer);
        return $insertAnswer;
    }

    private function updateCourseReportToCompleteIfNeeded(Reports $report)
    {
        $questions = $this->manager->getRepository('AppBundle:Course\CourseQuestions')->GetAllUnansweredQuestionsByCourseAndReport($report->getCourseId(), $report->getId());
        if(sizeof($questions) <= 0)
        {
            $report->setIsComplete(true);
            $report->setFinishedDateTime(new \DateTime());
            $this->manager->flush();
        }
    }

    private function validateSpecifiedReport(Reports $report)
    {
        if($report == null || !$report->getIsComplete()) throw new AccessDeniedException();

        if($this->authorizationService->isAuthorized())
        {
            $user = $this->authorizationService->getAuthorizedUserOrThrowException();
            if($report->getUserId() != $user->getId()) throw new AccessDeniedException();
        }
        else
        {
            if($report->getSessionId() != $this->session->getId()) throw new AccessDeniedException();
        }
    }
}