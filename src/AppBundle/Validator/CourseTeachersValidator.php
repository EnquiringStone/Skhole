<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 13-Mar-16
 * Time: 19:36
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;
use Doctrine\ORM\EntityManager;

class CourseTeachersValidator extends Validator
{
    public function __construct(EntityManager $manager, $rootDir)
    {
        parent::__construct($manager, $rootDir);
    }

    protected function validateName($name)
    {
        if(ValidatorHelper::isStringNullOrEmpty($name))
            throw new FrontEndException('course.edit.name.required', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($name))
            throw new FrontEndException('course.edit.name.illegal.characters', $this->domain);

        if(strlen($name) > 250)
            throw new FrontEndException('course.edit.name.too.long', $this->domain);
    }

    protected function validateDescription($description)
    {
        if(ValidatorHelper::containsCodingCharacters($description))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($description) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain);
    }

    protected function validatePictureUrl($pictureUrl)
    {
        //Nothing to validate. Validation is being done the moment it is uploaded.
        //See AjaxController
    }

    protected function validateTeacherId($id)
    {
        if(!ValidatorHelper::isStringNullOrEmpty($id))
            if(!ValidatorHelper::containsOnlyNumbers($id))
                throw new \Exception('Id has to be a number');
    }
}