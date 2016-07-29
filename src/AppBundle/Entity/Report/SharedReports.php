<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 28-Jul-16
 * Time: 15:17
 */

namespace AppBundle\Entity\Report;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="shared_reports")
 */
class SharedReports
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="report_id")
     */
    protected $reportId;

    /**
     * @ORM\Column(type="integer", name="user_id")
     */
    protected $userId;

    /**
     * @ORM\Column(type="integer", name="mentor_user_id")
     */
    protected $mentorUserId;

    /**
     * @ORM\Column(type="boolean", name="has_accepted")
     */
    protected $hasAccepted;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $message;

    /**
     * @ORM\Column(type="datetime", name="insert_date_time")
     */
    protected $insertDateTime;

    /**
     * @ORM\Column(type="boolean", name="has_revised")
     */
    protected $hasRevised;

    /**
     * @ORM\Column(type="integer", length=1, nullable=true)
     */
    protected $rating;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="mentor_user_id", referencedColumnName="id")
     */
    private $mentor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Report\Reports", inversedBy="sharedReports")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     */
    private $report;

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
     * Set reportId
     *
     * @param integer $reportId
     *
     * @return SharedReports
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return SharedReports
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
     * Set mentorUserId
     *
     * @param integer $mentorUserId
     *
     * @return SharedReports
     */
    public function setMentorUserId($mentorUserId)
    {
        $this->mentorUserId = $mentorUserId;

        return $this;
    }

    /**
     * Get mentorUserId
     *
     * @return integer
     */
    public function getMentorUserId()
    {
        return $this->mentorUserId;
    }

    /**
     * Set hasAccepted
     *
     * @param boolean $hasAccepted
     *
     * @return SharedReports
     */
    public function setHasAccepted($hasAccepted)
    {
        $this->hasAccepted = $hasAccepted;

        return $this;
    }

    /**
     * Get hasAccepted
     *
     * @return boolean
     */
    public function getHasAccepted()
    {
        return $this->hasAccepted;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return SharedReports
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set insertDateTime
     *
     * @param \DateTime $insertDateTime
     *
     * @return SharedReports
     */
    public function setInsertDateTime($insertDateTime)
    {
        $this->insertDateTime = $insertDateTime;

        return $this;
    }

    /**
     * Get insertDateTime
     *
     * @return \DateTime
     */
    public function getInsertDateTime()
    {
        return $this->insertDateTime;
    }

    /**
     * Set hasRevised
     *
     * @param boolean $hasRevised
     *
     * @return SharedReports
     */
    public function setHasRevised($hasRevised)
    {
        $this->hasRevised = $hasRevised;

        return $this;
    }

    /**
     * Get hasRevised
     *
     * @return boolean
     */
    public function getHasRevised()
    {
        return $this->hasRevised;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return SharedReports
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return SharedReports
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
     * Set mentor
     *
     * @param \AppBundle\Entity\User $mentor
     *
     * @return SharedReports
     */
    public function setMentor(\AppBundle\Entity\User $mentor = null)
    {
        $this->mentor = $mentor;

        return $this;
    }

    /**
     * Get mentor
     *
     * @return \AppBundle\Entity\User
     */
    public function getMentor()
    {
        return $this->mentor;
    }

    /**
     * Set report
     *
     * @param \AppBundle\Entity\Report\Reports $report
     *
     * @return SharedReports
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
}
