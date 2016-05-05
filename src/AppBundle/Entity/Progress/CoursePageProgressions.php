<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 05-May-16
 * Time: 15:50
 */

namespace AppBundle\Entity\Progress;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_page_progressions")
 */
class CoursePageProgressions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_progression_id")
     */
    protected $courseProgressionId;

    /**
     * @ORM\Column(type="integer", name="page_id")
     */
    protected $pageId;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\Column(type="datetime", name="start_date_time")
     */
    protected $startDateTime;

    /**
     * @ORM\Column(type="datetime", name="finished_date_time")
     */
    protected $finishedDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Progress\CourseProgressions", inversedBy="progressionPages")
     * @ORM\JoinColumn(name="course_progression_id", referencedColumnName="id")
     */
    private $courseProgression;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CoursePages")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Progress\CourseQuestionProgressions", mappedBy="pageProgression")
     */
    private $progressionQuestions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->progressionQuestions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set courseProgressionId
     *
     * @param integer $courseProgressionId
     *
     * @return CoursePageProgressions
     */
    public function setCourseProgressionId($courseProgressionId)
    {
        $this->courseProgressionId = $courseProgressionId;

        return $this;
    }

    /**
     * Get courseProgressionId
     *
     * @return integer
     */
    public function getCourseProgressionId()
    {
        return $this->courseProgressionId;
    }

    /**
     * Set pageId
     *
     * @param integer $pageId
     *
     * @return CoursePageProgressions
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CoursePageProgressions
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
     * Set startDateTime
     *
     * @param \DateTime $startDateTime
     *
     * @return CoursePageProgressions
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
     * @return CoursePageProgressions
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
     * Set courseProgression
     *
     * @param \AppBundle\Entity\Progress\CourseProgressions $courseProgression
     *
     * @return CoursePageProgressions
     */
    public function setCourseProgression(\AppBundle\Entity\Progress\CourseProgressions $courseProgression = null)
    {
        $this->courseProgression = $courseProgression;

        return $this;
    }

    /**
     * Get courseProgression
     *
     * @return \AppBundle\Entity\Progress\CourseProgressions
     */
    public function getCourseProgression()
    {
        return $this->courseProgression;
    }

    /**
     * Set page
     *
     * @param \AppBundle\Entity\Course\CoursePages $page
     *
     * @return CoursePageProgressions
     */
    public function setPage(\AppBundle\Entity\Course\CoursePages $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \AppBundle\Entity\Course\CoursePages
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Add progressionQuestion
     *
     * @param \AppBundle\Entity\Progress\CourseQuestionProgressions $progressionQuestion
     *
     * @return CoursePageProgressions
     */
    public function addProgressionQuestion(\AppBundle\Entity\Progress\CourseQuestionProgressions $progressionQuestion)
    {
        $this->progressionQuestions[] = $progressionQuestion;

        return $this;
    }

    /**
     * Remove progressionQuestion
     *
     * @param \AppBundle\Entity\Progress\CourseQuestionProgressions $progressionQuestion
     */
    public function removeProgressionQuestion(\AppBundle\Entity\Progress\CourseQuestionProgressions $progressionQuestion)
    {
        $this->progressionQuestions->removeElement($progressionQuestion);
    }

    /**
     * Get progressionQuestions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgressionQuestions()
    {
        return $this->progressionQuestions;
    }
}
