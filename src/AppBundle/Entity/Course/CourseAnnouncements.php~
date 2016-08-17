<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 23:29
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\UserReportInterface;
use AppBundle\Interfaces\Entity\UserStatisticsInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseAnnouncementsRepository")
 * @ORM\Table(name="course_announcements")
 */
class CourseAnnouncements implements UserStatisticsInterface, UserReportInterface
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
     * @ORM\Column(type="integer", name="user_inserted_id")
     */
    protected $userInsertedId;

    /**
     * @ORM\Column(type="datetime", name="insert_date_time")
     */
    protected $insertDateTime;

    /**
     * @ORM\Column(type="integer", name="user_updated_id", nullable=true)
     */
    protected $userUpdatedId;

    /**
     * @ORM\Column(type="datetime", name="update_date_time", nullable=true)
     */
    protected $updateDateTime;

    /**
     * @ORM\Column(type="integer", name="teacher_id", nullable=true)
     */
    protected $teacherId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    protected $contents;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseAnnouncements")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     */
    private $insertUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_updated_id", referencedColumnName="id")
     */
    private $updateUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Teachers")
     * @ORM\JoinColumn(name="teacher_id", referencedColumnName="id")
     */
    private $teacher;

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
     * @return CourseAnnouncements
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
     * Set userInsertedId
     *
     * @param integer $userInsertedId
     *
     * @return CourseAnnouncements
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
     * @return CourseAnnouncements
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
     * Set userUpdatedId
     *
     * @param integer $userUpdatedId
     *
     * @return CourseAnnouncements
     */
    public function setUserUpdatedId($userUpdatedId)
    {
        $this->userUpdatedId = $userUpdatedId;

        return $this;
    }

    /**
     * Get userUpdatedId
     *
     * @return integer
     */
    public function getUserUpdatedId()
    {
        return $this->userUpdatedId;
    }

    /**
     * Set updateDateTime
     *
     * @param \DateTime $updateDateTime
     *
     * @return CourseAnnouncements
     */
    public function setUpdateDateTime($updateDateTime)
    {
        $this->updateDateTime = $updateDateTime;

        return $this;
    }

    /**
     * Get updateDateTime
     *
     * @return \DateTime
     */
    public function getUpdateDateTime()
    {
        return $this->updateDateTime;
    }

    /**
     * Set teacherId
     *
     * @param integer $teacherId
     *
     * @return CourseAnnouncements
     */
    public function setTeacherId($teacherId)
    {
        $this->teacherId = $teacherId;

        return $this;
    }

    /**
     * Get teacherId
     *
     * @return integer
     */
    public function getTeacherId()
    {
        return $this->teacherId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return CourseAnnouncements
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contents
     *
     * @param string $contents
     *
     * @return CourseAnnouncements
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return CourseAnnouncements
     */
    public function setIsUndesirable($isUndesirable)
    {
        $this->isUndesirable = $isUndesirable;

        return $this;
    }

    /**
     * Get isUndesirable
     *
     * @return boolean
     */
    public function getIsUndesirable()
    {
        return $this->isUndesirable;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return CourseAnnouncements
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
     * @return CourseAnnouncements
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
     * Set updateUser
     *
     * @param \AppBundle\Entity\User $updateUser
     *
     * @return CourseAnnouncements
     */
    public function setUpdateUser(\AppBundle\Entity\User $updateUser = null)
    {
        $this->updateUser = $updateUser;

        return $this;
    }

    /**
     * Get updateUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getUpdateUser()
    {
        return $this->updateUser;
    }

    /**
     * Set teacher
     *
     * @param \AppBundle\Entity\Teachers $teacher
     *
     * @return CourseAnnouncements
     */
    public function setTeacher(\AppBundle\Entity\Teachers $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return \AppBundle\Entity\Teachers
     */
    public function getTeacher()
    {
        return $this->teacher;
    }
}
