<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 00:23
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_multiple_choice_answers")
 */
class CourseMultipleChoiceAnswers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_multiple_choice_id")
     */
    protected $courseMultipleChoiceId;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $answer;

    /**
     * @ORM\Column(type="integer")
     */
    protected $order;

    /**
     * Whether or not the question got correctly answered
     *
     * @ORM\Column(type="boolean", name="is_correct")
     */
    protected $isCorrect;

    /**
     * Whether or not this answer is the right one
     *
     * @ORM\Column(type="boolean", name="is_answer")
     */
    protected $isAnswer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseMultipleChoices", inversedBy="answers")
     * @ORM\JoinColumn(name="course_multiple_choice_id", referencedColumnName="id")
     */
    private $multipleChoiceQuestion;

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
     * Set courseMultipleChoiceId
     *
     * @param integer $courseMultipleChoiceId
     *
     * @return CourseMultipleChoiceAnswers
     */
    public function setCourseMultipleChoiceId($courseMultipleChoiceId)
    {
        $this->courseMultipleChoiceId = $courseMultipleChoiceId;

        return $this;
    }

    /**
     * Get courseMultipleChoiceId
     *
     * @return integer
     */
    public function getCourseMultipleChoiceId()
    {
        return $this->courseMultipleChoiceId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return CourseMultipleChoiceAnswers
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return CourseMultipleChoiceAnswers
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
     * Set isCorrect
     *
     * @param boolean $isCorrect
     *
     * @return CourseMultipleChoiceAnswers
     */
    public function setIsCorrect($isCorrect)
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * Get isCorrect
     *
     * @return boolean
     */
    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

    /**
     * Set isAnswer
     *
     * @param boolean $isAnswer
     *
     * @return CourseMultipleChoiceAnswers
     */
    public function setIsAnswer($isAnswer)
    {
        $this->isAnswer = $isAnswer;

        return $this;
    }

    /**
     * Get isAnswer
     *
     * @return boolean
     */
    public function getIsAnswer()
    {
        return $this->isAnswer;
    }

    /**
     * Set multipleChoiceQuestion
     *
     * @param \AppBundle\Entity\Course\CourseMultipleChoices $multipleChoiceQuestion
     *
     * @return CourseMultipleChoiceAnswers
     */
    public function setMultipleChoiceQuestion(\AppBundle\Entity\Course\CourseMultipleChoices $multipleChoiceQuestion = null)
    {
        $this->multipleChoiceQuestion = $multipleChoiceQuestion;

        return $this;
    }

    /**
     * Get multipleChoiceQuestion
     *
     * @return \AppBundle\Entity\Course\CourseMultipleChoices
     */
    public function getMultipleChoiceQuestion()
    {
        return $this->multipleChoiceQuestion;
    }
}
