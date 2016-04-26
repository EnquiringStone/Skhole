<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Mar-16
 * Time: 13:33
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseQuestionsRepository")
 * @ORM\Table(name="course_questions")
 */
class CourseQuestions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_page_id")
     */
    protected $coursePageId;

    /**
     * @ORM\Column(type="integer", name="question_type_id")
     */
    protected $questionTypeId;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $question;

    /**
     * @ORM\Column(type="text", nullable=true, length=16777215, name="answer_indication")
     */
    protected $answerIndication;

    /**
     * @ORM\Column(type="integer", name="question_order")
     */
    protected $questionOrder;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CoursePages", inversedBy="questions")
     * @ORM\JoinColumn(name="course_page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $coursePage;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseQuestionTypes")
     * @ORM\JoinColumn(name="question_type_id", referencedColumnName="id")
     */
    private $questionType;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseAnswers", mappedBy="courseQuestion")
     * @ORM\OrderBy({"answerOrder" = "ASC"})
     */
    private $courseAnswers;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courseAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set coursePageId
     *
     * @param integer $coursePageId
     *
     * @return CourseQuestions
     */
    public function setCoursePageId($coursePageId)
    {
        $this->coursePageId = $coursePageId;

        return $this;
    }

    /**
     * Get coursePageId
     *
     * @return integer
     */
    public function getCoursePageId()
    {
        return $this->coursePageId;
    }

    /**
     * Set questionTypeId
     *
     * @param integer $questionTypeId
     *
     * @return CourseQuestions
     */
    public function setQuestionTypeId($questionTypeId)
    {
        $this->questionTypeId = $questionTypeId;

        return $this;
    }

    /**
     * Get questionTypeId
     *
     * @return integer
     */
    public function getQuestionTypeId()
    {
        return $this->questionTypeId;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return CourseQuestions
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
     * Set answerIndication
     *
     * @param string $answerIndication
     *
     * @return CourseQuestions
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
     * Set questionOrder
     *
     * @param integer $questionOrder
     *
     * @return CourseQuestions
     */
    public function setQuestionOrder($questionOrder)
    {
        $this->questionOrder = $questionOrder;

        return $this;
    }

    /**
     * Get questionOrder
     *
     * @return integer
     */
    public function getQuestionOrder()
    {
        return $this->questionOrder;
    }

    /**
     * Set coursePage
     *
     * @param \AppBundle\Entity\Course\CoursePages $coursePage
     *
     * @return CourseQuestions
     */
    public function setCoursePage(\AppBundle\Entity\Course\CoursePages $coursePage = null)
    {
        $this->coursePage = $coursePage;

        return $this;
    }

    /**
     * Get coursePage
     *
     * @return \AppBundle\Entity\Course\CoursePages
     */
    public function getCoursePage()
    {
        return $this->coursePage;
    }

    /**
     * Set questionType
     *
     * @param \AppBundle\Entity\Course\CourseQuestionTypes $questionType
     *
     * @return CourseQuestions
     */
    public function setQuestionType(\AppBundle\Entity\Course\CourseQuestionTypes $questionType = null)
    {
        $this->questionType = $questionType;

        return $this;
    }

    /**
     * Get questionType
     *
     * @return \AppBundle\Entity\Course\CourseQuestionTypes
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Add courseAnswer
     *
     * @param \AppBundle\Entity\Course\CourseAnswers $courseAnswer
     *
     * @return CourseQuestions
     */
    public function addCourseAnswer(\AppBundle\Entity\Course\CourseAnswers $courseAnswer)
    {
        $this->courseAnswers[] = $courseAnswer;

        return $this;
    }

    /**
     * Remove courseAnswer
     *
     * @param \AppBundle\Entity\Course\CourseAnswers $courseAnswer
     */
    public function removeCourseAnswer(\AppBundle\Entity\Course\CourseAnswers $courseAnswer)
    {
        $this->courseAnswers->removeElement($courseAnswer);
    }

    /**
     * Get courseAnswers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseAnswers()
    {
        return $this->courseAnswers;
    }
}
