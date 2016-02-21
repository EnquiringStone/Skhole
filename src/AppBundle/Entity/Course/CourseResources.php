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
     * @ORM\Column(type="string", length=255, name="resource_url")
     */
    protected $resourceUrl;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $extension;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseResources")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

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
     * Set resourceUrl
     *
     * @param string $resourceUrl
     *
     * @return CourseResources
     */
    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;

        return $this;
    }

    /**
     * Get resourceUrl
     *
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return CourseResources
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

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
