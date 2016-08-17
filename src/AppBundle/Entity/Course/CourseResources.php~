<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 23:43
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\UserReportInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_resources")
 */
class CourseResources implements UserReportInterface
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
     * @ORM\Column(type="text", length=16777215, name="dropbox_url", nullable=true)
     */
    protected $dropboxUrl;

    /**
     * @ORM\Column(type="text", length=16777215, name="google_drive_url", nullable=true)
     */
    protected $googleDriveUrl;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseResources")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return CourseResources
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
     * @return CourseResources
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
     * Set dropboxUrl
     *
     * @param string $dropboxUrl
     *
     * @return CourseResources
     */
    public function setDropboxUrl($dropboxUrl)
    {
        $this->dropboxUrl = $dropboxUrl;

        return $this;
    }

    /**
     * Get dropboxUrl
     *
     * @return string
     */
    public function getDropboxUrl()
    {
        return $this->dropboxUrl;
    }

    /**
     * Set googleDriveUrl
     *
     * @param string $googleDriveUrl
     *
     * @return CourseResources
     */
    public function setGoogleDriveUrl($googleDriveUrl)
    {
        $this->googleDriveUrl = $googleDriveUrl;

        return $this;
    }

    /**
     * Get googleDriveUrl
     *
     * @return string
     */
    public function getGoogleDriveUrl()
    {
        return $this->googleDriveUrl;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return CourseResources
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
}
