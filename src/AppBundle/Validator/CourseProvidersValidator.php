<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 20-Mar-16
 * Time: 14:23
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseProvidersValidator extends Validator
{
    public function validateName($name)
    {
        if(ValidatorHelper::isStringNullOrEmpty($name))
            throw new FrontEndException('course.edit.name.required', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($name))
            throw new FrontEndException('course.edit.name.illegal.characters', $this->domain);

        if(strlen($name) > 250)
            throw new FrontEndException('course.edit.name.too.long', $this->domain);
    }

    public function validateDescription($description)
    {
        if(ValidatorHelper::containsCodingCharacters($description))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($description) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain);
    }

    protected function validateProviderId($id)
    {
        if(!ValidatorHelper::isStringNullOrEmpty($id))
            if(!ValidatorHelper::containsOnlyNumbers($id))
                throw new \Exception('Id has to be a number');
    }
}