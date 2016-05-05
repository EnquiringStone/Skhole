<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 05-May-16
 * Time: 16:11
 */

namespace AppBundle\Entity\Progress;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_answer_progressions")
 */
class CourseAnswerProgressions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_question_progression_id")
     */
    protected $courseQuestionProgressionId;

    /**
     * @ORM\Column(type="integer", name="answer_id")
     */
    protected $answerId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Progress\CourseQuestionProgressions", inversedBy="progressionAnswers")
     * @ORM\JoinColumn(name="course_question_progression_id", referencedColumnName="id")
     */
    private $questionProgression;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseAnswers")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    private $answer;

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
     * Set courseQuestionProgressionId
     *
     * @param integer $courseQuestionProgressionId
     *
     * @return CourseAnswerProgressions
     */
    public function setCourseQuestionProgressionId($courseQuestionProgressionId)
    {
        $this->courseQuestionProgressionId = $courseQuestionProgressionId;

        return $this;
    }

    /**
     * Get courseQuestionProgressionId
     *
     * @return integer
     */
    public function getCourseQuestionProgressionId()
    {
        return $this->courseQuestionProgressionId;
    }

    /**
     * Set answerId
     *
     * @param integer $answerId
     *
     * @return CourseAnswerProgressions
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
     * Set questionProgression
     *
     * @param \AppBundle\Entity\Progress\CourseQuestionProgressions $questionProgression
     *
     * @return CourseAnswerProgressions
     */
    public function setQuestionProgression(\AppBundle\Entity\Progress\CourseQuestionProgressions $questionProgression = null)
    {
        $this->questionProgression = $questionProgression;

        return $this;
    }

    /**
     * Get questionProgression
     *
     * @return \AppBundle\Entity\Progress\CourseQuestionProgressions
     */
    public function getQuestionProgression()
    {
        return $this->questionProgression;
    }

    /**
     * Set answer
     *
     * @param \AppBundle\Entity\Course\CourseAnswers $answer
     *
     * @return CourseAnswerProgressions
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
}
