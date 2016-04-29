<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 14:44
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class EducationProfileValidator extends Validator
{
    protected function validateClass($class)
    {
        if($class == null || $class == '')
            return;

        if(strlen($class) > 50)
            throw new FrontEndException('profile.education.class.too.long', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($class))
            throw new FrontEndException('profile.education.class.illegal.characters', $this->domain);
    }

    protected function validateSchoolYear($schoolYear)
    {
        if($schoolYear == null || $schoolYear == '')
            return;

        if(!ValidatorHelper::containsOnlyNumbers($schoolYear))
            throw new FrontEndException('profile.education.school.year.illegal.characters', $this->domain);

        $schoolYear = intval($schoolYear);
        if($schoolYear > 20)
            throw new FrontEndException('profile.education.school.year.too.high', $this->domain);

        if($schoolYear < 0)
            throw new FrontEndException('profile.education.school.year.too.low', $this->domain);
    }

    protected function validateLevel($level)
    {
        if($level == null || $level == '')
            return;

        if(strlen($level) > 50)
            throw new FrontEndException('profile.education.level.too.long', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($level))
            throw new FrontEndException('profile.education.level.illegal.character', $this->domain);
    }
}