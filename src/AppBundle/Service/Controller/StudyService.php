<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 06-Sep-16
 * Time: 17:16
 */

namespace AppBundle\Service\Controller;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Course\CoursePages;
use AppBundle\Enum\CoursePageTypeEnum;
use AppBundle\Enum\CourseStateEnum;
use AppBundle\Enum\PageTypeEnum;
use AppBundle\Exception\CourseRemovedException;
use AppBundle\Util\ValidatorHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StudyService
{
    private $sessionCourseId = 'lastCourseId';
    private $sessionPageId = 'lastPageId';
    private $sessionName = 'name';
    private $sessionPageType = 'pageType';

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    public function __construct(EntityManager $manager, Session $session, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->authorizationService = $authorizationService;
    }

    public function reset()
    {
        if ($this->session->has($this->sessionCourseId)) $this->session->remove($this->sessionCourseId);
        if ($this->session->has($this->sessionPageId)) $this->session->remove($this->sessionPageId);
        if ($this->session->has($this->sessionName)) $this->session->remove($this->sessionName);
        if ($this->session->has($this->sessionPageType)) $this->session->remove($this->sessionPageType);
    }

    /**
     * @param string $item
     * @return bool
     */
    public function hasItem($item)
    {
        return $this->session->has($item);
    }

    /**
     * @return \AppBundle\Entity\Course\Courses|null
     */
    public function getCourse()
    {
        if (!$this->hasItem($this->sessionCourseId)) return null;

        $id = $this->session->get($this->sessionCourseId);
        if (intval($id) <= 0) return null;
        return $this->manager->getRepository('AppBundle:Course\Courses')->find($id);
    }

    /**
     * @return CoursePages|null|
     */
    public function getPage()
    {
        if (!$this->hasItem($this->sessionPageId)) return null;

        $id = $this->session->get($this->sessionPageId);
        if (intval($id) <= 0) return null;

        return $this->manager->getRepository('AppBundle:Course\CoursePages')->find($id);
    }

    /**
     * @param int $courseId
     */
    public function switchToCourseId($courseId)
    {
        $this->reset();
        $this->session->set($this->sessionCourseId, $courseId);
    }

    /**
     * @param int $courseId
     * @param string $pageType
     * @param string $name
     */
    public function switchToNameAndPageType($courseId, $pageType, $name)
    {
        if (PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::CUSTOM_TYPE, $pageType) ||
            PageTypeEnum::matchValueWithGivenEnum(PageTypeEnum::class, PageTypeEnum::STANDARD_TYPE, $pageType))
        {
            $this->switchToCourseId($courseId);
            $this->session->set($this->sessionName, $name);
            $this->session->set($this->sessionPageType, $pageType);
        }
        else
        {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param int $courseId
     * @param int $pageId
     */
    public function switchToPageId($courseId, $pageId)
    {
        $this->switchToCourseId($courseId);
        $this->session->set($this->sessionPageId, $pageId);
    }

    /**
     * @param int $courseId
     * @param string $userCriteriaName
     * @return array
     */
    public function getCorrectCriteriaForUserOrSession($courseId, $userCriteriaName)
    {
        $criteria = array('courseId' => $courseId);
        if ($this->authorizationService->isAuthorized())
            $criteria[$userCriteriaName] = $this->authorizationService->getAuthorizedUserOrThrowException()->getId();
        else
            $criteria['sessionId'] = $this->session->getId();

        return $criteria;
    }

    /**
     * @param CoursePages $page
     * @return array
     */
    public function getCorrectCriteriaForCoursePageType(CoursePages $page)
    {
        $pageType = $page->getPageType()->getType();
        $criteria = array('type' => 'instruction.', 'name' => '');
        if (CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::ExerciseType, $pageType))
            $criteria['type'] = 'exercise.';
        elseif (CoursePageTypeEnum::matchValueWithGivenEnum(CoursePageTypeEnum::class, CoursePageTypeEnum::TextType, $pageType))
            $criteria['name'] = 'text.';
        else
        {
            if (!ValidatorHelper::isStringNullOrEmpty($page->getYoutubeUrl()) && !ValidatorHelper::isStringNullOrEmpty($page->getContents()))
                $criteria['name'] = 'video.text.';
            elseif (!ValidatorHelper::isStringNullOrEmpty($page->getYoutubeUrl()) && ValidatorHelper::isStringNullOrEmpty($page->getContents()))
                $criteria['name'] = 'video.';
            else
                $criteria['name'] = 'text.';
        }

        if ($criteria['type'] == 'exercise.') $criteria['name'] = '';

        return $criteria;
    }

    /**
     * @return array
     * @throws CourseRemovedException
     */
    public function getCorrectCriteriaForSession()
    {
        $criteria = array('courseId' => -1, 'redirectRoute' => 'app_home_study_course_page');
        if (!$this->hasItem($this->sessionCourseId)) return $criteria;
        $course = $this->getCourse();
        if ($course == null || $course->getState()->getStateCode() != CourseStateEnum::Finished)
        {
            $this->reset();
            return $criteria;
        }
        if ($course->getRemoved())
        {
            $this->reset();
            throw new CourseRemovedException();
        }
        $criteria['courseId'] = $this->session->get($this->sessionCourseId);
        if ($this->hasItem($this->sessionPageId))
        {
            $page = $this->getPage();
            if ($page != null && $page->getCourseId() == $course->getId())
            {
                $criteria['pageOrder'] = $page->getPageOrder();
                $criteria['redirectRoute'] = 'app_home_study_pages_page';
            }
            else
                $this->session->remove($this->sessionPageId); //Invalid page id
        }
        elseif ($this->hasItem($this->sessionName))
        {
            $criteria['pageType'] = $this->session->get($this->sessionPageType);
            $criteria['name'] = $this->session->get($this->sessionName);
            $criteria['redirectRoute'] = 'app_home_study_panels_page';
        }

        return $criteria;
    }

    /**
     * @return string
     */
    public function getSessionCourseId()
    {
        return $this->sessionCourseId;
    }

    /**
     * @return string
     */
    public function getSessionPageId()
    {
        return $this->sessionPageId;
    }

    /**
     * @return string
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * @return string
     */
    public function getSessionPageType()
    {
        return $this->sessionPageType;
    }
}