<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 14-May-16
 * Time: 10:10
 */

namespace AppBundle\Service\Ajax\Learn;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Course\CoursePages;
use AppBundle\Entity\Course\Courses;
use AppBundle\Entity\Progress\CoursePageProgressions;
use AppBundle\Entity\Progress\CourseProgressions;
use AppBundle\Entity\User;
use AppBundle\Enum\CoursePageTypeEnum;
use AppBundle\Interfaces\AjaxInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
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

    public function __construct(AuthorizationService $authorizationService, EntityManager $manager)
    {
        $this->authorizationService = $authorizationService;
        $this->manager = $manager;
    }

    public function saveStatistics($args)
    {
        if(!$this->authorizationService->isAuthorized()) return; //Do not save statistics for non-users

        $user = $this->authorizationService->getAuthorizedUserOrThrowException();
        $course = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['courseId']);

        if($course == null) throw new EntityNotFoundException();
        $this->isSpecifiedCourseValid($course);

        $page = $this->manager->getRepository('AppBundle:Course\CoursePages')->findOneBy(array('pageOrder' => $args['order'], 'courseId' => $args['courseId']));
        if($page == null) throw new EntityNotFoundException();

        $courseProgression = $this->createOrFindLoggedInCourseProgression($course, $user);

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
        return array();
    }

    private function isSpecifiedCourseValid(Courses $course)
    {
        if($course->getState()->getStateCode() == 'OK' && $course->getIsUndesirable() == false && $course->getRemoved() == false)
            return;

        throw new AccessDeniedException();
    }

    /**
     * @param Courses $course
     * @param User    $user
     * @return CourseProgressions
     */
    private function createOrFindLoggedInCourseProgression(Courses $course, User $user)
    {
        $progression = $this->manager->getRepository('AppBundle:Progress\CourseProgressions')->findOneBy(array('courseId' => $course->getId(), 'userId' => $user->getId()));
        if($progression == null)
        {
            $progression = new CourseProgressions();
            $progression->setCourse($course);
            $progression->setUser($user);
            $progression->setIsFinished(false);
            $progression->setStartDateTime(new \DateTime());
            $this->manager->persist($progression);
            $this->manager->flush();
        }
        return $progression;
    }

    private function createOrFindAnonymousCourseProgression(Courses $courses, $sessionId)
    {

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
}