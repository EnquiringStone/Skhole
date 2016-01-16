<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 14:24
 */

namespace AppBundle\Entity\LearnGroup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="skhole_learn_groups")
 */
class LearnGroups
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="course_id")
     */
    protected $courseId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, name="course_code", unique=true)
     */
    protected $courseCode;

    /**
     * @ORM\Column(type="integer", name="user_inserted_id")
     */
    protected $userInsertedId;

    /**
     * @ORM\Column(type="datetime", name="insert_date_time")
     */
    protected $insertDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     */
    private $insertUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LearnGroup\LearnGroupMembers", mappedBy="learnGroup")
     */
    private $members;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set courseId
     *
     * @param integer $courseId
     *
     * @return LearnGroups
     */
    public function setCourseId($courseId)
    {
        $this->courseId = $courseId;

        return $this;
    }

    /**
     * Get courseId
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return LearnGroups
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set courseCode
     *
     * @param string $courseCode
     *
     * @return LearnGroups
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;

        return $this;
    }

    /**
     * Get courseCode
     *
     * @return string
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Set userInsertedId
     *
     * @param integer $userInsertedId
     *
     * @return LearnGroups
     */
    public function setUserInsertedId($userInsertedId)
    {
        $this->userInsertedId = $userInsertedId;

        return $this;
    }

    /**
     * Get userInsertedId
     *
     * @return integer
     */
    public function getUserInsertedId()
    {
        return $this->userInsertedId;
    }

    /**
     * Set insertDateTime
     *
     * @param \DateTime $insertDateTime
     *
     * @return LearnGroups
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
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return LearnGroups
     */
    public function setCourse(\AppBundle\Entity\Course\Courses $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \AppBundle\Entity\Course\Courses
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set insertUser
     *
     * @param \AppBundle\Entity\User $insertUser
     *
     * @return LearnGroups
     */
    public function setInsertUser(\AppBundle\Entity\User $insertUser = null)
    {
        $this->insertUser = $insertUser;

        return $this;
    }

    /**
     * Get insertUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getInsertUser()
    {
        return $this->insertUser;
    }

    /**
     * Add member
     *
     * @param \AppBundle\Entity\LearnGroup\LearnGroupMembers $member
     *
     * @return LearnGroups
     */
    public function addMember(\AppBundle\Entity\LearnGroup\LearnGroupMembers $member)
    {
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param \AppBundle\Entity\LearnGroup\LearnGroupMembers $member
     */
    public function removeMember(\AppBundle\Entity\LearnGroup\LearnGroupMembers $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }
}
