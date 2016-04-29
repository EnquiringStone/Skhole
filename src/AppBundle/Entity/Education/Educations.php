<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 31-Jan-16
 * Time: 23:06
 */

namespace AppBundle\Entity\Education;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="educations")
 */
class Educations
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $class;

    /**
     * @ORM\Column(type="string", name="school_year", length=255, nullable=true)
     */
    protected $schoolYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $level;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Education\Organisations", mappedBy="education")
     */
    private $organisations;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->organisations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Educations
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
     * Set class
     *
     * @param string $class
     *
     * @return Educations
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set schoolYear
     *
     * @param string $schoolYear
     *
     * @return Educations
     */
    public function setSchoolYear($schoolYear)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    /**
     * Get schoolYear
     *
     * @return string
     */
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return Educations
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Educations
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
     * Add organisation
     *
     * @param \AppBundle\Entity\Education\Organisations $organisation
     *
     * @return Educations
     */
    public function addOrganisation(\AppBundle\Entity\Education\Organisations $organisation)
    {
        $this->organisations[] = $organisation;

        return $this;
    }

    /**
     * Remove organisation
     *
     * @param \AppBundle\Entity\Education\Organisations $organisation
     */
    public function removeOrganisation(\AppBundle\Entity\Education\Organisations $organisation)
    {
        $this->organisations->removeElement($organisation);
    }

    /**
     * Get organisations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrganisations()
    {
        return $this->organisations;
    }
}
