<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 00:13
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="skhole_course_open_questions")
 */
class CourseOpenQuestions
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
     * @ORM\Column(type="text", length=16777215)
     */
    protected $question;

    /**
     * @ORM\Column(type="integer")
     */
    protected $order;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $score;

    /**
     * @ORM\Column(type="text", length=16777215, name="answer_indication")
     */
    protected $answerIndication;

    /**
     * @ORM\Column(type="boolean", name="is_finished")
     */
    protected $isFinished;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseExercises", inversedBy="openQuestions")
     * @ORM\JoinColumn(name="course_exercise_id", referencedColumnName="id")
     */
    private $courseExercise;

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
     * @return CourseOpenQuestions
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
     * Set question
     *
     * @param string $question
     *
     * @return CourseOpenQuestions
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
     * Set order
     *
     * @param integer $order
     *
     * @return CourseOpenQuestions
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
     * Set score
     *
     * @param integer $score
     *
     * @return CourseOpenQuestions
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set answerIndication
     *
     * @param string $answerIndication
     *
     * @return CourseOpenQuestions
     */
    public function setAnswerIndication($answerIndication)
    {
        $this->answerIndication = $answerIndication;

        return $this;
    }

    /**
     * Get answerIndication
     *
     * @return string
     */
    public function getAnswerIndication()
    {
        return $this->answerIndication;
    }

    /**
     * Set isFinished
     *
     * @param boolean $isFinished
     *
     * @return CourseOpenQuestions
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
     * @return CourseOpenQuestions
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
}
