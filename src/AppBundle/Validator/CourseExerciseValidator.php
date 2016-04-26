<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 25-Apr-16
 * Time: 14:16
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseExerciseValidator extends Validator
{
    public function validateTitle($title)
    {
        if(ValidatorHelper::isStringNullOrEmpty($title))
            throw new FrontEndException('course.edit.card.title.empty', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($title))
            throw new FrontEndException('course.edit.card.illegal.characters', $this->domain);

        if(strlen($title) > 250)
            throw new FrontEndException('course.edit.card.too.long', $this->domain);
    }
}