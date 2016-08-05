<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 21:57
 */

namespace AppBundle\Entity\Course;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="course_levels")
 */
class CourseLevels
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=150, name="level_short")
     */
    protected $levelShort;

    /**
     * @ORM\Column(type="string", length=500, name="level_long")
     */
    protected $levelLong;

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
     * Set levelShort
     *
     * @param string $levelShort
     *
     * @return CourseLevels
     */
    public function setLevelShort($levelShort)
    {
        $this->levelShort = $levelShort;

        return $this;
    }

    /**
     * Get levelShort
     *
     * @return string
     */
    public function getLevelShort()
    {
        return $this->levelShort;
    }

    /**
     * Set levelLong
     *
     * @param string $levelLong
     *
     * @return CourseLevels
     */
    public function setLevelLong($levelLong)
    {
        $this->levelLong = $levelLong;

        return $this;
    }

    /**
     * Get levelLong
     *
     * @return string
     */
    public function getLevelLong()
    {
        return $this->levelLong;
    }
}
