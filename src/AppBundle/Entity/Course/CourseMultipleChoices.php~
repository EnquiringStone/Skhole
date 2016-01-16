<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 00:19
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="skhole_course_multiple_choices")
 */
class CourseMultipleChoices
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_exercise_id")
     */
    protected $courseExerciseId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $order;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $question;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseExercises", inversedBy="multipleChoices")
     * @ORM\JoinColumn(name="course_exercise_id", referencedColumnName="id")
     */
    private $courseExercise;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseMultipleChoiceAnswers", mappedBy="multipleChoiceQuestion")
     */
    private $answers;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set courseExerciseId
     *
     * @param integer $courseExerciseId
     *
     * @return CourseMultipleChoices
     */
    public function setCourseExerciseId($courseExerciseId)
    {
        $this->courseExerciseId = $courseExerciseId;

        return $this;
    }

    /**
     * Get courseExerciseId
     *
     * @return integer
     */
    public function getCourseExerciseId()
    {
        return $this->courseExerciseId;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return CourseMultipleChoices
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
     * Set question
     *
     * @param string $question
     *
     * @return CourseMultipleChoices
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CourseMultipleChoices
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
     * Set courseExercise
     *
     * @param \AppBundle\Entity\Course\CourseExercises $courseExercise
     *
     * @return CourseMultipleChoices
     */
    public function setCourseExercise(\AppBundle\Entity\Course\CourseExercises $courseExercise = null)
    {
        $this->courseExercise = $courseExercise;

        return $this;
    }

    /**
     * Get courseExercise
     *
     * @return \AppBundle\Entity\Course\CourseExercises
     */
    public function getCourseExercise()
    {
        return $this->courseExercise;
    }

    /**
     * Add answer
     *
     * @param \AppBundle\Entity\Course\CourseMultipleChoiceAnswers $answer
     *
     * @return CourseMultipleChoices
     */
    public function addAnswer(\AppBundle\Entity\Course\CourseMultipleChoiceAnswers $answer)
    {
        $this->answers[] = $answer;

        return $this;
    }

    /**
     * Remove answer
     *
     * @param \AppBundle\Entity\Course\CourseMultipleChoiceAnswers $answer
     */
    public function removeAnswer(\AppBundle\Entity\Course\CourseMultipleChoiceAnswers $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
