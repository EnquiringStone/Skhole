<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Feb-16
 * Time: 14:55
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;

class CourseCardsValidator extends Validator
{


    public function validateDescription($description)
    {
        if(ValidatorHelper::containsCodingCharacters($description))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($description) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain, array('%size%' => strlen($description)));
    }

    public function validateYoutubeUrl($url)
    {
        if(!ValidatorHelper::isYoutubeUrl($url))
            throw new FrontEndException('course.edit.card.youtube.url', $this->domain);
    }

    public function validateName($name)
    {
        if(ValidatorHelper::isStringNullOrEmpty($name))
            throw new FrontEndException('course.edit.card.title.empty', $this->domain);

        if(!ValidatorHelper::containsOnlyCharactersAndNumbers($name))
            throw new FrontEndException('course.edit.card.illegal.characters', $this->domain);

        if(strlen($name) > 250)
            throw new FrontEndException('course.edit.card.too.long', $this->domain);
    }

    public function validatePriorKnowledge($knowledge)
    {
        if(ValidatorHelper::containsCodingCharacters($knowledge))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($knowledge) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain, array('%size%' => strlen($knowledge)));
    }
}