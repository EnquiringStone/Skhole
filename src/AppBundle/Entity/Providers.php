<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 22:47
 */

namespace AppBundle\Entity;

use AppBundle\Interfaces\Entity\BasicDetailsInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="providers")
 */
class Providers implements BasicDetailsInterface
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
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", length=16777215, nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_inserted_id", referencedColumnName="id")
     */
    private $insertUser;

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
     * @return Providers
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
     * Set name
     *
     * @param string $name
     *
     * @return Providers
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
     * @return Providers
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
     * Set insertUser
     *
     * @param \AppBundle\Entity\User $insertUser
     *
     * @return Providers
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
}
