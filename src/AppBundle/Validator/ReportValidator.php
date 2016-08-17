<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 07-Aug-16
 * Time: 00:00
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class ReportValidator extends Validator
{
    protected function validateCoursePart($coursePart)
    {
        if(!in_array($coursePart, array('Other', 'CourseCard', 'CourseMaterial', 'CourseReview', 'Announcement', 'CourseSchedule')))
            throw new FrontEndException('report.course.part.invalid', $this->domain);
    }

    protected function validateEmail($email)
    {
        if(ValidatorHelper::isStringNullOrEmpty($email))
            throw new FrontEndException('contact.email.empty', $this->domain);

        if(!ValidatorHelper::isValidEmail($email))
            throw new FrontEndException('contact.email.invalid', $this->domain);
    }

    protected function validatePhone($phone)
    {
        if(ValidatorHelper::isStringNullOrEmpty($phone))
            return;

        if(!ValidatorHelper::isValidPhoneNumber($phone))
            throw new FrontEndException('report.phone.invalid', $this->domain);
    }

    protected function validateMessage($message)
    {
        if(ValidatorHelper::isStringNullOrEmpty($message))
            throw new FrontEndException('report.message.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($message))
            throw new FrontEndException('report.message.illegal.characters', $this->domain);
    }

    protected function validateName($name)
    {
        if(!ValidatorHelper::containsOnlyNumbers($name))
            throw new FrontEndException('report.name.invalid', $this->domain);

        if($name == -1)
            throw new FrontEndException('report.name.not.found', $this->domain);
    }

    protected function validatePage($page)
    {
        if($page == -1) return;

        if(!ValidatorHelper::containsOnlyNumbers($page))
            throw new FrontEndException('report.page.invalid', $this->domain);
    }

    protected function validateSubject($subject)
    {
        if(!in_array($subject, array('Copyright', 'Undesirable', 'Other')))
            throw new FrontEndException('report.subject.invalid', $this->domain);
    }
}