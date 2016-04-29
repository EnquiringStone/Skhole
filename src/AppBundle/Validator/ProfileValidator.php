<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 00:26
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class ProfileValidator extends Validator
{
    protected function validateFirstName($firstName)
    {
        if(ValidatorHelper::isStringNullOrEmpty($firstName))
            throw new FrontEndException('profile.edit.first.name.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($firstName) || ValidatorHelper::containsOnlyNumbers($firstName))
            throw new FrontEndException('profile.edit.first.name.illegal.characters', $this->domain);

        if(strlen($firstName) > 255)
            throw new FrontEndException('profile.edit.first.name.too.long', $this->domain);
    }

    protected function validateSurname($surname)
    {
        if(ValidatorHelper::isStringNullOrEmpty($surname))
            throw new FrontEndException('profile.edit.surname.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($surname) || ValidatorHelper::containsOnlyNumbers($surname))
            throw new FrontEndException('profile.edit.surname.illegal.characters', $this->domain);

        if(strlen($surname) > 255)
            throw new FrontEndException('profile.edit.surname.too.long', $this->domain);
    }

    protected function validateEmail($email)
    {
        if(ValidatorHelper::isStringNullOrEmpty($email))
            throw new FrontEndException('profile.edit.email.empty', $this->domain);

        if(!ValidatorHelper::isValidEmail($email))
            throw new FrontEndException('profile.edit.email.invalid', $this->domain);
    }

    protected function validateDateOfBirth($dateOfBirth)
    {
        if($dateOfBirth == null)
            return;

        if($dateOfBirth > new \DateTime())
            throw new FrontEndException('profile.edit.date.of.birth.invalid', $this->domain);
    }

    protected function validateCountry($country)
    {

    }

    protected function validateLanguage($language)
    {

    }

    protected function validatePicture($picture)
    {
        //Nothing to validate. Validation is being done the moment it is uploaded.
        //See AjaxController
    }
}