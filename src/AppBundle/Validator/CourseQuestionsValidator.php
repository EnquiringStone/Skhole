<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 16:27
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseQuestionsValidator extends Validator
{
    public function validateQuestion($question)
    {
        if(ValidatorHelper::isStringNullOrEmpty($question))
            throw new FrontEndException('course.edit.question.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($question))
            throw new FrontEndException('course.edit.question.contains.coding', $this->domain);

        if(strlen($question) > 2500)
            throw new FrontEndException('course.edit.question.too.long', $this->domain);
    }

    public function validateAnswerIndication($answerIndication)
    {
        if($answerIndication == null)
            return;

        if(ValidatorHelper::isStringNullOrEmpty($answerIndication))
            throw new FrontEndException('course.edit.question.answer.indication.empty', $this->domain);

        if(strlen($answerIndication) > 15000)
            throw new FrontEndException('course.edit.question.answer.indication.too.long', $this->domain, array('%size%' => strlen($answerIndication)));

        if(ValidatorHelper::containsCodingCharacters($answerIndication))
            throw new FrontEndException('course.edit.question.answer.indication.contains.coding', $this->domain);
    }
}