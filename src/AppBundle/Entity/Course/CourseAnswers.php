<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Mar-16
 * Time: 13:50
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_answers")
 */
class CourseAnswers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_question_id")
     */
    protected $courseQuestionId;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $answer;

    /**
     * @ORM\Column(type="integer", name="answer_order")
     */
    protected $answerOrder;

    /**
     * @ORM\Column(type="boolean", name="is_correct")
     */
    protected $isCorrect;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseQuestions", inversedBy="courseAnswers")
     * @ORM\JoinColumn(name="course_question_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $courseQuestion;

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
     * Set courseQuestionId
     *
     * @param integer $courseQuestionId
     *
     * @return CourseAnswers
     */
    public function setCourseQuestionId($courseQuestionId)
    {
        $this->courseQuestionId = $courseQuestionId;

        return $this;
    }

    /**
     * Get courseQuestionId
     *
     * @return integer
     */
    public function getCourseQuestionId()
    {
        return $this->courseQuestionId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return CourseAnswers
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
     * Set answerOrder
     *
     * @param integer $answerOrder
     *
     * @return CourseAnswers
     */
    public function setAnswerOrder($answerOrder)
    {
        $this->answerOrder = $answerOrder;

        return $this;
    }

    /**
     * Get answerOrder
     *
     * @return integer
     */
    public function getAnswerOrder()
    {
        return $this->answerOrder;
    }

    /**
     * Set isCorrect
     *
     * @param boolean $isCorrect
     *
     * @return CourseAnswers
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
     * Set courseQuestion
     *
     * @param \AppBundle\Entity\Course\CourseQuestions $courseQuestion
     *
     * @return CourseAnswers
     */
    public function setCourseQuestion(\AppBundle\Entity\Course\CourseQuestions $courseQuestion = null)
    {
        $this->courseQuestion = $courseQuestion;

        return $this;
    }

    /**
     * Get courseQuestion
     *
     * @return \AppBundle\Entity\Course\CourseQuestions
     */
    public function getCourseQuestion()
    {
        return $this->courseQuestion;
    }
}
