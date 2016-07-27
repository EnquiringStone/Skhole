<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 05-May-16
 * Time: 15:40
 */

namespace AppBundle\Entity\Progress;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_progressions")
 */
class CourseProgressions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_id")
     */
    protected $courseId;

    /**
     * @ORM\Column(type="integer", name="user_id", nullable=true)
     */
    protected $userId;

    /**
     * @ORM\Column(type="string", name="session_id", nullable=true)
     */
    protected $sessionId;

    /**
     * @ORM\Column(type="datetime", name="start_date_time")
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime", name="finished_date_time")
     */
    protected $finishedDateTime;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Progress\CoursePageProgressions", mappedBy="courseProgression")
     */
    private $progressionPages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->progressionPages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set courseId
     *
     * @param integer $courseId
     *
     * @return CourseProgressions
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;

        return $this;
    }

    /**
     * Get courseId
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return CourseProgressions
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     *
     * @return CourseProgressions
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set startDateTime
     *
     * @param \DateTime $startDateTime
     *
     * @return CourseProgressions
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    /**
     * Get startDateTime
     *
     * @return \DateTime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * Set finishedDateTime
     *
     * @param \DateTime $finishedDateTime
     *
     * @return CourseProgressions
     */
    public function setFinishedDateTime($finishedDateTime)
    {
        $this->finishedDateTime = $finishedDateTime;

        return $this;
    }

    /**
     * Get finishedDateTime
     *
     * @return \DateTime
     */
    public function getFinishedDateTime()
    {
        return $this->finishedDateTime;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CourseProgressions
     */
    public function setIsFinished($isFinished)
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    /**
     * Get isFinished
     *
     * @return boolean
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return CourseProgressions
     */
    public function setCourse(\AppBundle\Entity\Course\Courses $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \AppBundle\Entity\Course\Courses
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return CourseProgressions
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add progressionPage
     *
     * @param \AppBundle\Entity\Progress\CoursePageProgressions $progressionPage
     *
     * @return CourseProgressions
     */
    public function addProgressionPage(\AppBundle\Entity\Progress\CoursePageProgressions $progressionPage)
    {
        $this->progressionPages[] = $progressionPage;

        return $this;
    }

    /**
     * Remove progressionPage
     *
     * @param \AppBundle\Entity\Progress\CoursePageProgressions $progressionPage
     */
    public function removeProgressionPage(\AppBundle\Entity\Progress\CoursePageProgressions $progressionPage)
    {
        $this->progressionPages->removeElement($progressionPage);
    }

    /**
     * Get progressionPages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgressionPages()
    {
        return $this->progressionPages;
    }
}
