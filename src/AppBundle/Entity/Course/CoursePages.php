<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Mar-16
 * Time: 13:14
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoursePagesRepository")
 * @ORM\Table(name="course_pages")
 */
class CoursePages
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
     * @ORM\Column(type="integer", name="page_type_id")
     */
    protected $pageTypeId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $contents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="youtube_url")
     */
    protected $youtubeUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="youtube_embed_url")
     */
    protected $youtubeEmbedUrl;

    /**
     * @ORM\Column(type="integer", name="page_order")
     */
    protected $pageOrder;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="coursePages")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CoursePageTypes")
     * @ORM\JoinColumn(name="page_type_id", referencedColumnName="id")
     */
    private $pageType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseQuestions", mappedBy="coursePage")
     * @ORM\OrderBy({"questionOrder" = "ASC"})
     */
    private $questions;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CoursePages
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
     * Set pageTypeId
     *
     * @param integer $pageTypeId
     *
     * @return CoursePages
     */
    public function setPageTypeId($pageTypeId)
    {
        $this->pageTypeId = $pageTypeId;

        return $this;
    }

    /**
     * Get pageTypeId
     *
     * @return integer
     */
    public function getPageTypeId()
    {
        return $this->pageTypeId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return CoursePages
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
     * @return CoursePages
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
     * @return CoursePages
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
     * Set youtubeEmbedUrl
     *
     * @param string $youtubeEmbedUrl
     *
     * @return CoursePages
     */
    public function setYoutubeEmbedUrl($youtubeEmbedUrl)
    {
        $this->youtubeEmbedUrl = $youtubeEmbedUrl;

        return $this;
    }

    /**
     * Get youtubeEmbedUrl
     *
     * @return string
     */
    public function getYoutubeEmbedUrl()
    {
        return $this->youtubeEmbedUrl;
    }

    /**
     * Set pageOrder
     *
     * @param integer $pageOrder
     *
     * @return CoursePages
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
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return CoursePages
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
     * @return CoursePages
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
     * Set pageType
     *
     * @param \AppBundle\Entity\Course\CoursePageTypes $pageType
     *
     * @return CoursePages
     */
    public function setPageType(\AppBundle\Entity\Course\CoursePageTypes $pageType = null)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * Get pageType
     *
     * @return \AppBundle\Entity\Course\CoursePageTypes
     */
    public function getPageType()
    {
        return $this->pageType;
    }

    /**
     * Add question
     *
     * @param \AppBundle\Entity\Course\CourseQuestions $question
     *
     * @return CoursePages
     */
    public function addQuestion(\AppBundle\Entity\Course\CourseQuestions $question)
    {
        $this->questions[] = $question;

        return $this;
    }

    /**
     * Remove question
     *
     * @param \AppBundle\Entity\Course\CourseQuestions $question
     */
    public function removeQuestion(\AppBundle\Entity\Course\CourseQuestions $question)
    {
        $this->questions->removeElement($question);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
