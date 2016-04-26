<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 16:40
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseAnswersValidator extends Validator
{
    public function validateAnswer($answer)
    {
        if(ValidatorHelper::isStringNullOrEmpty($answer))
            throw new FrontEndException('course.edit.answer.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($answer))
            throw new FrontEndException('course.edit.answer.contains.coding', $this->domain);

        if(strlen($answer) > 250)
            throw new FrontEndException('course.edit.answer.too.long', $this->domain);
    }

    public function validateAnswerId($answerId)
    {}

    public function validateCorrect($correct)
    {}
}