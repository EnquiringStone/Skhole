<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 20:39
 */

namespace AppBundle\Entity\Course;

use AppBundle\Interfaces\Entity\BasicDetailsInterface;
use AppBundle\Interfaces\Entity\UserReportInterface;
use AppBundle\Interfaces\Entity\UserStatisticsInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoursesRepository")
 * @ORM\Table(name="courses")
 */
class Courses implements UserStatisticsInterface, UserReportInterface, BasicDetailsInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", name="language_id", nullable=true)
     */
    protected $languageId;

    /**
     * @ORM\Column(type="integer", name="level_id", nullable=true)
     */
    protected $levelId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $difficulty;

    /**
     * @ORM\Column(type="integer", name="estimated_duration", nullable=true)
     */
    protected $estimatedDuration;

    /**
     * @ORM\Column(type="integer", name="state_id")
     */
    protected $stateId;

    /**
     * @ORM\Column(type="datetime", name="state_changed", nullable=true)
     */
    protected $stateChanged;

    /**
     * @ORM\Column(type="boolean", name="is_undesirable")
     */
    protected $isUndesirable;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $removed;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=5, name="average_content_rating", nullable=true)
     */
    protected $averageContentRating;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views;

    /**
     * @ORM\Column(type="integer")
     */
    protected $pages;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseLanguages")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseLevels")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     */
    private $level;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Course\CourseCards", mappedBy="course")
     */
    private $courseCard;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Course\CourseSchedules", mappedBy="course")
     */
    private $courseSchedule;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseAnnouncements", mappedBy="course", fetch="EXTRA_LAZY")
     */
    private $courseAnnouncements;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseResources", mappedBy="course", fetch="EXTRA_LAZY")
     */
    private $courseResources;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseViews", mappedBy="course", fetch="EXTRA_LAZY")
     */
    private $courseViews;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseExercises", mappedBy="course", fetch="EXTRA_LAZY")
     */
    private $courseExercises;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseInstructions", mappedBy="course", fetch="EXTRA_LAZY")
     */
    private $courseInstructions;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Course\CourseStates")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Course\CourseReviews", mappedBy="course")
     */
    private $courseReviews;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tags")
     * @ORM\JoinTable
     * (
     *     name="skhole_course_tags",
     *     joinColumns=
     *     {
     *          @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     *      },
     *     inverseJoinColumns=
     *     {
     *          @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     *     }
     * )
     */
    private $tags;

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
     * Set userInsertedId
     *
     * @param integer $userInsertedId
     *
     * @return Courses
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
     * @return Courses
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
     * @return Courses
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
     * @return Courses
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
     * Set name
     *
     * @param string $name
     *
     * @return Courses
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
     * @return Courses
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
     * Set languageId
     *
     * @param integer $languageId
     *
     * @return Courses
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get languageId
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Set levelId
     *
     * @param integer $levelId
     *
     * @return Courses
     */
    public function setLevelId($levelId)
    {
        $this->levelId = $levelId;

        return $this;
    }

    /**
     * Get levelId
     *
     * @return integer
     */
    public function getLevelId()
    {
        return $this->levelId;
    }

    /**
     * Set difficulty
     *
     * @param integer $difficulty
     *
     * @return Courses
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return integer
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set estimatedDuration
     *
     * @param integer $estimatedDuration
     *
     * @return Courses
     */
    public function setEstimatedDuration($estimatedDuration)
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    /**
     * Get estimatedDuration
     *
     * @return integer
     */
    public function getEstimatedDuration()
    {
        return $this->estimatedDuration;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     *
     * @return Courses
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;

        return $this;
    }

    /**
     * Get stateId
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set stateChanged
     *
     * @param \DateTime $stateChanged
     *
     * @return Courses
     */
    public function setStateChanged($stateChanged)
    {
        $this->stateChanged = $stateChanged;

        return $this;
    }

    /**
     * Get stateChanged
     *
     * @return \DateTime
     */
    public function getStateChanged()
    {
        return $this->stateChanged;
    }

    /**
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return Courses
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
     * Set averageContentRating
     *
     * @param string $averageContentRating
     *
     * @return Courses
     */
    public function setAverageContentRating($averageContentRating)
    {
        $this->averageContentRating = $averageContentRating;

        return $this;
    }

    /**
     * Get averageContentRating
     *
     * @return string
     */
    public function getAverageContentRating()
    {
        return $this->averageContentRating;
    }

    /**
     * Set insertUser
     *
     * @param \AppBundle\Entity\User $insertUser
     *
     * @return Courses
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
     * @return Courses
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
     * Set language
     *
     * @param \AppBundle\Entity\Course\CourseLanguages $language
     *
     * @return Courses
     */
    public function setLanguage(\AppBundle\Entity\Course\CourseLanguages $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \AppBundle\Entity\Course\CourseLanguages
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set level
     *
     * @param \AppBundle\Entity\Course\CourseLevels $level
     *
     * @return Courses
     */
    public function setLevel(\AppBundle\Entity\Course\CourseLevels $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \AppBundle\Entity\Course\CourseLevels
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set courseCard
     *
     * @param \AppBundle\Entity\Course\CourseCards $courseCard
     *
     * @return Courses
     */
    public function setCourseCard(\AppBundle\Entity\Course\CourseCards $courseCard = null)
    {
        $this->courseCard = $courseCard;

        return $this;
    }

    /**
     * Get courseCard
     *
     * @return \AppBundle\Entity\Course\CourseCards
     */
    public function getCourseCard()
    {
        return $this->courseCard;
    }

    /**
     * Set courseSchedule
     *
     * @param \AppBundle\Entity\Course\CourseSchedules $courseSchedule
     *
     * @return Courses
     */
    public function setCourseSchedule(\AppBundle\Entity\Course\CourseSchedules $courseSchedule = null)
    {
        $this->courseSchedule = $courseSchedule;

        return $this;
    }

    /**
     * Get courseSchedule
     *
     * @return \AppBundle\Entity\Course\CourseSchedules
     */
    public function getCourseSchedule()
    {
        return $this->courseSchedule;
    }

    /**
     * Add courseAnnouncement
     *
     * @param \AppBundle\Entity\Course\CourseAnnouncements $courseAnnouncement
     *
     * @return Courses
     */
    public function addCourseAnnouncement(\AppBundle\Entity\Course\CourseAnnouncements $courseAnnouncement)
    {
        $this->courseAnnouncements[] = $courseAnnouncement;

        return $this;
    }

    /**
     * Remove courseAnnouncement
     *
     * @param \AppBundle\Entity\Course\CourseAnnouncements $courseAnnouncement
     */
    public function removeCourseAnnouncement(\AppBundle\Entity\Course\CourseAnnouncements $courseAnnouncement)
    {
        $this->courseAnnouncements->removeElement($courseAnnouncement);
    }

    /**
     * Get courseAnnouncements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseAnnouncements()
    {
        return $this->courseAnnouncements;
    }

    /**
     * Add courseResource
     *
     * @param \AppBundle\Entity\Course\CourseResources $courseResource
     *
     * @return Courses
     */
    public function addCourseResource(\AppBundle\Entity\Course\CourseResources $courseResource)
    {
        $this->courseResources[] = $courseResource;

        return $this;
    }

    /**
     * Remove courseResource
     *
     * @param \AppBundle\Entity\Course\CourseResources $courseResource
     */
    public function removeCourseResource(\AppBundle\Entity\Course\CourseResources $courseResource)
    {
        $this->courseResources->removeElement($courseResource);
    }

    /**
     * Get courseResources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseResources()
    {
        return $this->courseResources;
    }

    /**
     * Set state
     *
     * @param \AppBundle\Entity\Course\CourseStates $state
     *
     * @return Courses
     */
    public function setState(\AppBundle\Entity\Course\CourseStates $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \AppBundle\Entity\Course\CourseStates
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add tag
     *
     * @param \AppBundle\Entity\Tags $tag
     *
     * @return Courses
     */
    public function addTag(\AppBundle\Entity\Tags $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \AppBundle\Entity\Tags $tag
     */
    public function removeTag(\AppBundle\Entity\Tags $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add courseView
     *
     * @param \AppBundle\Entity\Course\CourseViews $courseView
     *
     * @return Courses
     */
    public function addCourseView(\AppBundle\Entity\Course\CourseViews $courseView)
    {
        $this->courseViews[] = $courseView;

        return $this;
    }

    /**
     * Remove courseView
     *
     * @param \AppBundle\Entity\Course\CourseViews $courseView
     */
    public function removeCourseView(\AppBundle\Entity\Course\CourseViews $courseView)
    {
        $this->courseViews->removeElement($courseView);
    }

    /**
     * Get courseViews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseViews()
    {
        return $this->courseViews;
    }

    /**
     * Add courseExercise
     *
     * @param \AppBundle\Entity\Course\CourseExercises $courseExercise
     *
     * @return Courses
     */
    public function addCourseExercise(\AppBundle\Entity\Course\CourseExercises $courseExercise)
    {
        $this->courseExercises[] = $courseExercise;

        return $this;
    }

    /**
     * Remove courseExercise
     *
     * @param \AppBundle\Entity\Course\CourseExercises $courseExercise
     */
    public function removeCourseExercise(\AppBundle\Entity\Course\CourseExercises $courseExercise)
    {
        $this->courseExercises->removeElement($courseExercise);
    }

    /**
     * Get courseExercises
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseExercises()
    {
        return $this->courseExercises;
    }

    /**
     * Add courseInstruction
     *
     * @param \AppBundle\Entity\Course\CourseInstructions $courseInstruction
     *
     * @return Courses
     */
    public function addCourseInstruction(\AppBundle\Entity\Course\CourseInstructions $courseInstruction)
    {
        $this->courseInstructions[] = $courseInstruction;

        return $this;
    }

    /**
     * Remove courseInstruction
     *
     * @param \AppBundle\Entity\Course\CourseInstructions $courseInstruction
     */
    public function removeCourseInstruction(\AppBundle\Entity\Course\CourseInstructions $courseInstruction)
    {
        $this->courseInstructions->removeElement($courseInstruction);
    }

    /**
     * Get courseInstructions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseInstructions()
    {
        return $this->courseInstructions;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Courses
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set pages
     *
     * @param integer $pages
     * @return Courses
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return integer
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Add courseReview
     *
     * @param \AppBundle\Entity\Course\CourseReviews $courseReview
     *
     * @return Courses
     */
    public function addCourseReview(\AppBundle\Entity\Course\CourseReviews $courseReview)
    {
        $this->courseReviews[] = $courseReview;

        return $this;
    }

    /**
     * Remove courseReview
     *
     * @param \AppBundle\Entity\Course\CourseReviews $courseReview
     */
    public function removeCourseReview(\AppBundle\Entity\Course\CourseReviews $courseReview)
    {
        $this->courseReviews->removeElement($courseReview);
    }

    /**
     * Get courseReviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseReviews()
    {
        return $this->courseReviews;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courseAnnouncements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseResources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseViews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseExercises = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseInstructions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseReviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set removed
     *
     * @param boolean $removed
     *
     * @return Courses
     */
    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    /**
     * Get removed
     *
     * @return boolean
     */
    public function getRemoved()
    {
        return $this->removed;
    }

    public function isComplete()
    {
        //TODO Implement
        return false;
    }

    public function canPublish()
    {
        //TODO Implement
        return false;
    }

    public function hasCustomPages()
    {
        return $this->courseExercises->count() > 0 || $this->courseInstructions->count() > 0;
    }
}
