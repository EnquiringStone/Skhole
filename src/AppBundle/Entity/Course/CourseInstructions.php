<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 00:04
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\UserReportInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_instructions")
 */
class CourseInstructions implements UserReportInterface
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
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $contents;

    /**
     * @ORM\Column(type="string", length=255, name="youtube_url", nullable=true)
     */
    protected $youtubeUrl;

    /**
     * @ORM\Column(type="integer", name="page_order")
     */
    protected $pageOrder;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseInstructions")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

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
     * @return CourseInstructions
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
     * Set title
     *
     * @param string $title
     *
     * @return CourseInstructions
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contents
     *
     * @param string $contents
     *
     * @return CourseInstructions
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set youtubeUrl
     *
     * @param string $youtubeUrl
     *
     * @return CourseInstructions
     */
    public function setYoutubeUrl($youtubeUrl)
    {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    /**
     * Get youtubeUrl
     *
     * @return string
     */
    public function getYoutubeUrl()
    {
        return $this->youtubeUrl;
    }

    /**
     * Set pageOrder
     *
     * @param integer $pageOrder
     *
     * @return CourseInstructions
     */
    public function setPageOrder($pageOrder)
    {
        $this->pageOrder = $pageOrder;

        return $this;
    }

    /**
     * Get pageOrder
     *
     * @return integer
     */
    public function getPageOrder()
    {
        return $this->pageOrder;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CourseInstructions
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
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return CourseInstructions
     */
    public function setIsUndesirable($isUndesirable)
    {
        $this->isUndesirable = $isUndesirable;

        return $this;
    }

    /**
     * Get isUndesirable
     *
     * @return boolean
     */
    public function getIsUndesirable()
    {
        return $this->isUndesirable;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return CourseInstructions
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
}
