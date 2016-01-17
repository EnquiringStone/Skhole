<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 14:34
 */

namespace AppBundle\Entity\LearnGroup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="skhole_learn_group_members")
 */
class LearnGroupMembers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="user_id")
     */
    protected $userId;

    /**
     * @ORM\Column(type="integer", name="learn_group_id")
     */
    protected $learnGroupId;

    /**
     * @ORM\Column(type="datetime", name="joined_at")
     */
    protected $joinedAt;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, name="exercise_progress", nullable=true)
     */
    protected $exerciseProgress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, name="instruction_progress", nullable=true)
     */
    protected $instructionProgress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, name="total_progress", nullable=true)
     */
    protected $totalProgress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, name="correct_multiple_choice_average", nullable=true)
     */
    protected $correctMultipleChoiceAverage;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5, name="open_question_score_average", nullable=true)
     */
    protected $openQuestionScoreAverage;

    /**
     * @ORM\Column(type="integer", name="final_mark", nullable=true)
     */
    protected $finalMark;

    /**
     * @ORM\Column(type="boolean", name="is_complete")
     */
    protected $isComplete;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LearnGroup\LearnGroups", inversedBy="members")
     * @ORM\JoinColumn(name="learn_group_id", referencedColumnName="id")
     */
    private $learnGroup;

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
     * Set userId
     *
     * @param integer $userId
     *
     * @return LearnGroupMembers
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set learnGroupId
     *
     * @param integer $learnGroupId
     *
     * @return LearnGroupMembers
     */
    public function setLearnGroupId($learnGroupId)
    {
        $this->learnGroupId = $learnGroupId;

        return $this;
    }

    /**
     * Get learnGroupId
     *
     * @return integer
     */
    public function getLearnGroupId()
    {
        return $this->learnGroupId;
    }

    /**
     * Set joinedAt
     *
     * @param \DateTime $joinedAt
     *
     * @return LearnGroupMembers
     */
    public function setJoinedAt($joinedAt)
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    /**
     * Get joinedAt
     *
     * @return \DateTime
     */
    public function getJoinedAt()
    {
        return $this->joinedAt;
    }

    /**
     * Set exerciseProgress
     *
     * @param \number $exerciseProgress
     *
     * @return LearnGroupMembers
     */
    public function setExerciseProgress(\number $exerciseProgress)
    {
        $this->exerciseProgress = $exerciseProgress;

        return $this;
    }

    /**
     * Get exerciseProgress
     *
     * @return \number
     */
    public function getExerciseProgress()
    {
        return $this->exerciseProgress;
    }

    /**
     * Set instructionProgress
     *
     * @param \number $instructionProgress
     *
     * @return LearnGroupMembers
     */
    public function setInstructionProgress(\number $instructionProgress)
    {
        $this->instructionProgress = $instructionProgress;

        return $this;
    }

    /**
     * Get instructionProgress
     *
     * @return \number
     */
    public function getInstructionProgress()
    {
        return $this->instructionProgress;
    }

    /**
     * Set totalProgress
     *
     * @param \number $totalProgress
     *
     * @return LearnGroupMembers
     */
    public function setTotalProgress(\number $totalProgress)
    {
        $this->totalProgress = $totalProgress;

        return $this;
    }

    /**
     * Get totalProgress
     *
     * @return \number
     */
    public function getTotalProgress()
    {
        return $this->totalProgress;
    }

    /**
     * Set correctMultipleChoiceAverage
     *
     * @param \number $correctMultipleChoiceAverage
     *
     * @return LearnGroupMembers
     */
    public function setCorrectMultipleChoiceAverage(\number $correctMultipleChoiceAverage)
    {
        $this->correctMultipleChoiceAverage = $correctMultipleChoiceAverage;

        return $this;
    }

    /**
     * Get correctMultipleChoiceAverage
     *
     * @return \number
     */
    public function getCorrectMultipleChoiceAverage()
    {
        return $this->correctMultipleChoiceAverage;
    }

    /**
     * Set openQuestionScoreAverage
     *
     * @param \number $openQuestionScoreAverage
     *
     * @return LearnGroupMembers
     */
    public function setOpenQuestionScoreAverage(\number $openQuestionScoreAverage)
    {
        $this->openQuestionScoreAverage = $openQuestionScoreAverage;

        return $this;
    }

    /**
     * Get openQuestionScoreAverage
     *
     * @return \number
     */
    public function getOpenQuestionScoreAverage()
    {
        return $this->openQuestionScoreAverage;
    }

    /**
     * Set finalMark
     *
     * @param integer $finalMark
     *
     * @return LearnGroupMembers
     */
    public function setFinalMark($finalMark)
    {
        $this->finalMark = $finalMark;

        return $this;
    }

    /**
     * Get finalMark
     *
     * @return integer
     */
    public function getFinalMark()
    {
        return $this->finalMark;
    }

    /**
     * Set isComplete
     *
     * @param boolean $isComplete
     *
     * @return LearnGroupMembers
     */
    public function setIsComplete($isComplete)
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    /**
     * Get isComplete
     *
     * @return boolean
     */
    public function getIsComplete()
    {
        return $this->isComplete;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return LearnGroupMembers
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set learnGroup
     *
     * @param \AppBundle\Entity\LearnGroup\LearnGroups $learnGroup
     *
     * @return LearnGroupMembers
     */
    public function setLearnGroup(\AppBundle\Entity\LearnGroup\LearnGroups $learnGroup = null)
    {
        $this->learnGroup = $learnGroup;

        return $this;
    }

    /**
     * Get learnGroup
     *
     * @return \AppBundle\Entity\LearnGroup\LearnGroups
     */
    public function getLearnGroup()
    {
        return $this->learnGroup;
    }
}
