<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 28-Jul-16
 * Time: 15:16
 */

namespace AppBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="multiple_choice_answers")
 */
class MultipleChoiceAnswers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="answer_id")
     */
    protected $answerId;

    /**
     * @ORM\Column(type="integer", name="answer_result_id")
     */
    protected $answerResultId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseAnswers")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Report\AnswerResults", inversedBy="multipleChoiceAnswers")
     * @ORM\JoinColumn(name="answer_result_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $answerResult;

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
     * Set answerId
     *
     * @param integer $answerId
     *
     * @return MultipleChoiceAnswers
     */
    public function setAnswerId($answerId)
    {
        $this->answerId = $answerId;

        return $this;
    }

    /**
     * Get answerId
     *
     * @return integer
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Set answerResultId
     *
     * @param integer $answerResultId
     *
     * @return MultipleChoiceAnswers
     */
    public function setAnswerResultId($answerResultId)
    {
        $this->answerResultId = $answerResultId;

        return $this;
    }

    /**
     * Get answerResultId
     *
     * @return integer
     */
    public function getAnswerResultId()
    {
        return $this->answerResultId;
    }

    /**
     * Set answer
     *
     * @param \AppBundle\Entity\Course\CourseAnswers $answer
     *
     * @return MultipleChoiceAnswers
     */
    public function setAnswer(\AppBundle\Entity\Course\CourseAnswers $answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return \AppBundle\Entity\Course\CourseAnswers
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set answerResult
     *
     * @param \AppBundle\Entity\Report\AnswerResults $answerResult
     *
     * @return MultipleChoiceAnswers
     */
    public function setAnswerResult(\AppBundle\Entity\Report\AnswerResults $answerResult = null)
    {
        $this->answerResult = $answerResult;

        return $this;
    }

    /**
     * Get answerResult
     *
     * @return \AppBundle\Entity\Report\AnswerResults
     */
    public function getAnswerResult()
    {
        return $this->answerResult;
    }
}
