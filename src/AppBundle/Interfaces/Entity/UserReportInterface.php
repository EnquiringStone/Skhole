<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-Jan-16
 * Time: 23:57
 */

namespace AppBundle\Interfaces\Entity;


interface UserReportInterface
{
    /**
     * Set isUndesirable
     *
     * @param boolean $isUndesirable
     *
     * @return Courses
     */
    public function setIsUndesirable($isUndesirable);

    /**
     * Get isUndesirable
     *
     * @return boolean
     */
    public function getIsUndesirable();
}