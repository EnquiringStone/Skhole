<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 23:01
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\BasicDetailsInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="course_cards")
 */
class CourseCards implements BasicDetailsInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="youtube_url")
     */
    protected $youtubeUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="youtube_embed_url")
     */
    protected $youtubeEmbedUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="picture_url")
     */
    protected $pictureUrl;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true, name="prior_knowledge")
     */
    protected $priorKnowledge;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Course\Courses", inversedBy="courseCard")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Teachers")
     * @ORM\JoinTable
     * (
     *      name="skhole_course_teachers",
     *      joinColumns=
     *      {
     *          @ORM\JoinColumn(name="course_card_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns=
     *      {
     *          @ORM\JoinColumn(name="teacher_id", referencedColumnName="id")
     *      }
     * )
     */
    private $teachers;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Providers")
     * @ORM\JoinTable
     * (
     *      name="skhole_course_providers",
     *      joinColumns=
     *      {
     *          @ORM\JoinColumn(name="course_card_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns=
     *      {
     *          @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     *      }
     * )
     */
    private $providers;

    public function isComplete()
    {
        //TODO implement
        return false;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->teachers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->providers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CourseCards
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
     * @return CourseCards
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
     * Set description
     *
     * @param string $description
     *
     * @return CourseCards
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set youtubeUrl
     *
     * @param string $youtubeUrl
     *
     * @return CourseCards
     */
    public function setYoutubeUrl($youtubeUrl)
    {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    /**
     * Get youtubeUrl
     *
     * @return string
     */
    public function getYoutubeUrl()
    {
        return $this->youtubeUrl;
    }

    /**
     * Set pictureUrl
     *
     * @param string $pictureUrl
     *
     * @return CourseCards
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    /**
     * Get pictureUrl
     *
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    /**
     * Set course
     *
     * @param \AppBundle\Entity\Course\Courses $course
     *
     * @return CourseCards
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
     * Add teacher
     *
     * @param \AppBundle\Entity\Teachers $teacher
     *
     * @return CourseCards
     */
    public function addTeacher(\AppBundle\Entity\Teachers $teacher)
    {
        $this->teachers[] = $teacher;

        return $this;
    }

    /**
     * Remove teacher
     *
     * @param \AppBundle\Entity\Teachers $teacher
     */
    public function removeTeacher(\AppBundle\Entity\Teachers $teacher)
    {
        $this->teachers->removeElement($teacher);
    }

    /**
     * Get teachers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeachers()
    {
        return $this->teachers;
    }

    /**
     * Add provider
     *
     * @param \AppBundle\Entity\Providers $provider
     *
     * @return CourseCards
     */
    public function addProvider(\AppBundle\Entity\Providers $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Remove provider
     *
     * @param \AppBundle\Entity\Providers $provider
     */
    public function removeProvider(\AppBundle\Entity\Providers $provider)
    {
        $this->providers->removeElement($provider);
    }

    /**
     * Get providers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Set priorKnowledge
     *
     * @param string $priorKnowledge
     *
     * @return CourseCards
     */
    public function setPriorKnowledge($priorKnowledge)
    {
        $this->priorKnowledge = $priorKnowledge;

        return $this;
    }

    /**
     * Get priorKnowledge
     *
     * @return string
     */
    public function getPriorKnowledge()
    {
        return $this->priorKnowledge;
    }

    /**
     * Set youtubeEmbedUrl
     *
     * @param string $youtubeEmbedUrl
     *
     * @return CourseCards
     */
    public function setYoutubeEmbedUrl($youtubeEmbedUrl)
    {
        $this->youtubeEmbedUrl = $youtubeEmbedUrl;

        return $this;
    }

    /**
     * Get youtubeEmbedUrl
     *
     * @return string
     */
    public function getYoutubeEmbedUrl()
    {
        return $this->youtubeEmbedUrl;
    }
}
