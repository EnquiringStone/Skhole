<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 00:37
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\UserReportInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_exercises")
 */
class CourseExercises implements UserReportInterface
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
     * @ORM\Column(type="integer")
     */
    protected $order;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseExercises")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseMultipleChoices", mappedBy="courseExercise")
     */
    private $multipleChoices;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseOpenQuestions", mappedBy="courseExercise")
     */
    private $openQuestions;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->multipleChoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->openQuestions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CourseExercises
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
     * @return CourseExercises
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
     * Set order
     *
     * @param integer $order
     *
     * @return CourseExercises
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CourseExercises
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
     * @return CourseExercises
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
     * @return CourseExercises
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
     * Add multipleChoice
     *
     * @param \AppBundle\Entity\Course\CourseMultipleChoices $multipleChoice
     *
     * @return CourseExercises
     */
    public function addMultipleChoice(\AppBundle\Entity\Course\CourseMultipleChoices $multipleChoice)
    {
        $this->multipleChoices[] = $multipleChoice;

        return $this;
    }

    /**
     * Remove multipleChoice
     *
     * @param \AppBundle\Entity\Course\CourseMultipleChoices $multipleChoice
     */
    public function removeMultipleChoice(\AppBundle\Entity\Course\CourseMultipleChoices $multipleChoice)
    {
        $this->multipleChoices->removeElement($multipleChoice);
    }

    /**
     * Get multipleChoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMultipleChoices()
    {
        return $this->multipleChoices;
    }

    /**
     * Add openQuestion
     *
     * @param \AppBundle\Entity\Course\CourseOpenQuestions $openQuestion
     *
     * @return CourseExercises
     */
    public function addOpenQuestion(\AppBundle\Entity\Course\CourseOpenQuestions $openQuestion)
    {
        $this->openQuestions[] = $openQuestion;

        return $this;
    }

    /**
     * Remove openQuestion
     *
     * @param \AppBundle\Entity\Course\CourseOpenQuestions $openQuestion
     */
    public function removeOpenQuestion(\AppBundle\Entity\Course\CourseOpenQuestions $openQuestion)
    {
        $this->openQuestions->removeElement($openQuestion);
    }

    /**
     * Get openQuestions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpenQuestions()
    {
        return $this->openQuestions;
    }
}
