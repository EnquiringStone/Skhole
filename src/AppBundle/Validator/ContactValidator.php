<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 06-Aug-16
 * Time: 18:24
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class ContactValidator extends Validator
{
    protected function validateEmail($email)
    {
        if(ValidatorHelper::isStringNullOrEmpty($email))
            throw new FrontEndException('contact.email.empty', $this->domain);

        if(!ValidatorHelper::isValidEmail($email))
            throw new FrontEndException('contact.email.invalid', $this->domain);
    }

    protected function validateFirstName($firstName)
    {
        if(ValidatorHelper::isStringNullOrEmpty($firstName))
            throw new FrontEndException('contact.first.name.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($firstName))
            throw new FrontEndException('contact.first.name.coding.characters', $this->domain);
    }

    protected function validateSurname($surname)
    {
        if(ValidatorHelper::isStringNullOrEmpty($surname))
            throw new FrontEndException('contact.surname.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($surname))
            throw new FrontEndException('contact.surname.coding.characters', $this->domain);
    }

    protected function validateSubject($subject)
    {
        if(!ValidatorHelper::containsOnlyNumbers($subject))
            throw new FrontEndException('contact.subject.invalid', $this->domain);
    }

    protected function validateMessage($message)
    {
        if(ValidatorHelper::isStringNullOrEmpty($message))
            throw new FrontEndException('contact.message.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($message))
            throw new FrontEndException('contact.message.coding.characters', $this->domain);
    }
}