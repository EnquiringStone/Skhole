<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 19-Mar-16
 * Time: 14:37
 */

namespace AppBundle\Validator;

use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseAnnouncementsValidator extends Validator
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

    public function validateContents($contents)
    {
        if(ValidatorHelper::isStringNullOrEmpty($contents))
            throw new FrontEndException('course.edit.contents.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($contents))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($contents) > 15000)
            throw new FrontEndException('course.edit.contents.too.long', $this->domain, array('%size%' => strlen($contents)));
    }
}