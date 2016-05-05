<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 05-May-16
 * Time: 16:00
 */

namespace AppBundle\Entity\Progress;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_question_progressions")
 */
class CourseQuestionProgressions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_page_progression_id")
     */
    protected $coursePageProgressionId;

    /**
     * @ORM\Column(type="integer", name="question_id")
     */
    protected $questionId;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $givenAnswer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Progress\CoursePageProgressions", inversedBy="progressionQuestions")
     * @ORM\JoinColumn(name="course_page_progression_id", referencedColumnName="id")
     */
    private $pageProgression;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseQuestions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Progress\CourseAnswerProgressions", mappedBy="questionProgression")
     */
    private $progressionAnswers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->progressionAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set coursePageProgressionId
     *
     * @param integer $coursePageProgressionId
     *
     * @return CourseQuestionProgressions
     */
    public function setCoursePageProgressionId($coursePageProgressionId)
    {
        $this->coursePageProgressionId = $coursePageProgressionId;

        return $this;
    }

    /**
     * Get coursePageProgressionId
     *
     * @return integer
     */
    public function getCoursePageProgressionId()
    {
        return $this->coursePageProgressionId;
    }

    /**
     * Set questionId
     *
     * @param integer $questionId
     *
     * @return CourseQuestionProgressions
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }

    /**
     * Get questionId
     *
     * @return integer
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * Set givenAnswer
     *
     * @param string $givenAnswer
     *
     * @return CourseQuestionProgressions
     */
    public function setGivenAnswer($givenAnswer)
    {
        $this->givenAnswer = $givenAnswer;

        return $this;
    }

    /**
     * Get givenAnswer
     *
     * @return string
     */
    public function getGivenAnswer()
    {
        return $this->givenAnswer;
    }

    /**
     * Set pageProgression
     *
     * @param \AppBundle\Entity\Progress\CoursePageProgressions $pageProgression
     *
     * @return CourseQuestionProgressions
     */
    public function setPageProgression(\AppBundle\Entity\Progress\CoursePageProgressions $pageProgression = null)
    {
        $this->pageProgression = $pageProgression;

        return $this;
    }

    /**
     * Get pageProgression
     *
     * @return \AppBundle\Entity\Progress\CoursePageProgressions
     */
    public function getPageProgression()
    {
        return $this->pageProgression;
    }

    /**
     * Set question
     *
     * @param \AppBundle\Entity\Course\CourseQuestions $question
     *
     * @return CourseQuestionProgressions
     */
    public function setQuestion(\AppBundle\Entity\Course\CourseQuestions $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \AppBundle\Entity\Course\CourseQuestions
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Add progressionAnswer
     *
     * @param \AppBundle\Entity\Progress\CourseAnswerProgressions $progressionAnswer
     *
     * @return CourseQuestionProgressions
     */
    public function addProgressionAnswer(\AppBundle\Entity\Progress\CourseAnswerProgressions $progressionAnswer)
    {
        $this->progressionAnswers[] = $progressionAnswer;

        return $this;
    }

    /**
     * Remove progressionAnswer
     *
     * @param \AppBundle\Entity\Progress\CourseAnswerProgressions $progressionAnswer
     */
    public function removeProgressionAnswer(\AppBundle\Entity\Progress\CourseAnswerProgressions $progressionAnswer)
    {
        $this->progressionAnswers->removeElement($progressionAnswer);
    }

    /**
     * Get progressionAnswers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProgressionAnswers()
    {
        return $this->progressionAnswers;
    }
}
