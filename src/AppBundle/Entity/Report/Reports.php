<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 28-Jul-16
 * Time: 15:14
 */

namespace AppBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportsRepository")
 * @ORM\Table(name="reports")
 */
class Reports
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\Column(type="datetime", name="insert_date_time")
     */
    protected $insertDateTime;

    /**
     * @ORM\Column(type="datetime", name="finished_date_time", nullable=true)
     */
    protected $finishedDateTime;

    /**
     * @ORM\Column(type="boolean", name="is_complete")
     */
    protected $isComplete;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\AnswerResults", mappedBy="report")
     */
    private $answerResults;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\SharedReports", mappedBy="report")
     */
    private $sharedReports;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answerResults = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedReports = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Reports
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
     * @return Reports
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
     * @return Reports
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
     * Set insertDateTime
     *
     * @param \DateTime $insertDateTime
     *
     * @return Reports
     */
    public function setInsertDateTime($insertDateTime)
    {
        $this->insertDateTime = $insertDateTime;

        return $this;
    }

    /**
     * Get insertDateTime
     *
     * @return \DateTime
     */
    public function getInsertDateTime()
    {
        return $this->insertDateTime;
    }

    /**
     * Set isComplete
     *
     * @param boolean $isComplete
     *
     * @return Reports
     */
    public function setIsComplete($isComplete)
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    /**
     * Get isComplete
     *
     * @return boolean
     */
    public function getIsComplete()
    {
        return $this->isComplete;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return Reports
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
     * @return Reports
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
     * Add answerResult
     *
     * @param \AppBundle\Entity\Report\AnswerResults $answerResult
     *
     * @return Reports
     */
    public function addAnswerResult(\AppBundle\Entity\Report\AnswerResults $answerResult)
    {
        $this->answerResults[] = $answerResult;

        return $this;
    }

    /**
     * Remove answerResult
     *
     * @param \AppBundle\Entity\Report\AnswerResults $answerResult
     */
    public function removeAnswerResult(\AppBundle\Entity\Report\AnswerResults $answerResult)
    {
        $this->answerResults->removeElement($answerResult);
    }

    /**
     * Get answerResults
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswerResults()
    {
        return $this->answerResults;
    }

    /**
     * Add sharedReport
     *
     * @param \AppBundle\Entity\Report\SharedReports $sharedReport
     *
     * @return Reports
     */
    public function addSharedReport(\AppBundle\Entity\Report\SharedReports $sharedReport)
    {
        $this->sharedReports[] = $sharedReport;

        return $this;
    }

    /**
     * Remove sharedReport
     *
     * @param \AppBundle\Entity\Report\SharedReports $sharedReport
     */
    public function removeSharedReport(\AppBundle\Entity\Report\SharedReports $sharedReport)
    {
        $this->sharedReports->removeElement($sharedReport);
    }

    /**
     * Get sharedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSharedReports()
    {
        return $this->sharedReports;
    }

    /**
     * Set finishedDateTime
     *
     * @param \DateTime $finishedDateTime
     *
     * @return Reports
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
}
