<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Feb-16
 * Time: 16:23
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseSchedulesValidator extends Validator
{
    public function validateSchedule($schedule)
    {
        if(ValidatorHelper::containsCodingCharacters($schedule))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($schedule) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain, array('%size%' => strlen($schedule)));
    }
}