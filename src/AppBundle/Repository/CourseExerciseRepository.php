<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 01:24
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CourseExerciseRepository extends EntityRepository
{
    public function getContentEntity($contentEnum)
    {
        $entity = 'AppBundle\Entity\Course\\'.$contentEnum;


    }
}