<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 20:43
 */

namespace AppBundle\Interfaces\Entity;

/**
 * Interface UserStatistics
 *
 * Implement after running doctrine:generate:entities
 *
 * @package AppBundle\Interfaces\Entity
 */
interface UserStatisticsInterface
{
    /**
     * Set userInsertedId
     *
     * @param integer $userInsertedId
     *
     * @return Courses
     */
    public function setUserInsertedId($userInsertedId);

    /**
     * Get userInsertedId
     *
     * @return integer
     */
    public function getUserInsertedId();

    /**
     * Set insertDateTime
     *
     * @param \DateTime $insertDateTime
     *
     * @return Courses
     */
    public function setInsertDateTime($insertDateTime);

    /**
     * Get insertDateTime
     *
     * @return \DateTime
     */
    public function getInsertDateTime();

    /**
     * Set userUpdatedId
     *
     * @param integer $userUpdatedId
     *
     * @return Courses
     */
    public function setUserUpdatedId($userUpdatedId);

    /**
     * Get userUpdatedId
     *
     * @return integer
     */
    public function getUserUpdatedId();

    /**
     * Set updateDateTime
     *
     * @param \DateTime $updateDateTime
     *
     * @return Courses
     */
    public function setUpdateDateTime($updateDateTime);

    /**
     * Get updateDateTime
     *
     * @return \DateTime
     */
    public function getUpdateDateTime();
}