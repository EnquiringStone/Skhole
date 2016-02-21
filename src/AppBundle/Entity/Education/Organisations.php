<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 31-Jan-16
 * Time: 23:18
 */

namespace AppBundle\Entity\Education;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="organisations")
 */
class Organisations
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=16777215)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", name="organisation_id")
     */
    private $organisationId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Education\Educations", inversedBy="organisations")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     */
    private $education;

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
     * Set name
     *
     * @param string $name
     *
     * @return Organisations
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
     * Set organisationId
     *
     * @param integer $organisationId
     *
     * @return Organisations
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;

        return $this;
    }

    /**
     * Get organisationId
     *
     * @return integer
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * Set organisation
     *
     * @param \AppBundle\Entity\Education\Educations $organisation
     *
     * @return Organisations
     */
    public function setOrganisation(\AppBundle\Entity\Education\Educations $organisation = null)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get organisation
     *
     * @return \AppBundle\Entity\Education\Educations
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set education
     *
     * @param \AppBundle\Entity\Education\Educations $education
     *
     * @return Organisations
     */
    public function setEducation(\AppBundle\Entity\Education\Educations $education = null)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return \AppBundle\Entity\Education\Educations
     */
    public function getEducation()
    {
        return $this->education;
    }
}
