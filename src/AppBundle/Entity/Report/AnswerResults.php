<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 28-Jul-16
 * Time: 15:15
 */

namespace AppBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnswerResultsRepository")
 * @ORM\Table(name="answer_results")
 */
class AnswerResults
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="question_id")
     */
    protected $questionId;

    /**
     * @ORM\Column(type="integer", name="report_id")
     */
    protected $reportId;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $answer;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseQuestions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Report\Reports", inversedBy="answerResults")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $report;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report\MultipleChoiceAnswers", mappedBy="answerResult")
     */
    private $multipleChoiceAnswers;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->multipleChoiceAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set questionId
     *
     * @param integer $questionId
     *
     * @return AnswerResults
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
     * Set reportId
     *
     * @param integer $reportId
     *
     * @return AnswerResults
     */
    public function setReportId($reportId)
    {
        $this->reportId = $reportId;

        return $this;
    }

    /**
     * Get reportId
     *
     * @return integer
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return AnswerResults
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
     * Set question
     *
     * @param \AppBundle\Entity\Course\CourseQuestions $question
     *
     * @return AnswerResults
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
     * Set report
     *
     * @param \AppBundle\Entity\Report\Reports $report
     *
     * @return AnswerResults
     */
    public function setReport(\AppBundle\Entity\Report\Reports $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \AppBundle\Entity\Report\Reports
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Add multipleChoiceAnswer
     *
     * @param \AppBundle\Entity\Report\MultipleChoiceAnswers $multipleChoiceAnswer
     *
     * @return AnswerResults
     */
    public function addMultipleChoiceAnswer(\AppBundle\Entity\Report\MultipleChoiceAnswers $multipleChoiceAnswer)
    {
        $this->multipleChoiceAnswers[] = $multipleChoiceAnswer;

        return $this;
    }

    /**
     * Remove multipleChoiceAnswer
     *
     * @param \AppBundle\Entity\Report\MultipleChoiceAnswers $multipleChoiceAnswer
     */
    public function removeMultipleChoiceAnswer(\AppBundle\Entity\Report\MultipleChoiceAnswers $multipleChoiceAnswer)
    {
        $this->multipleChoiceAnswers->removeElement($multipleChoiceAnswer);
    }

    /**
     * Get multipleChoiceAnswers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMultipleChoiceAnswers()
    {
        return $this->multipleChoiceAnswers;
    }
}
