<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 21:20
 */

namespace AppBundle\Interfaces\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interface BasicDetails
 *
 * Implement after running doctrine:generate:entities
 *
 * @package AppBundle\Util\Entity
 */
interface BasicDetailsInterface
{
    /**
     * Set name
     *
     * @param string $name
     *
     * @return Courses
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Courses
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();
}