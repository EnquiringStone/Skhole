<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Mar-16
 * Time: 14:31
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;
use Doctrine\ORM\EntityManager;

class CoursePagesValidator extends Validator
{
    private $type;

    public function __construct(EntityManager $manager, $rootDir, $types)
    {
        parent::__construct($manager, $rootDir);
        $this->type = $types['type'];
    }

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
        if(ValidatorHelper::isStringNullOrEmpty($contents) && $this->type == 'text')
            throw new FrontEndException('course.edit.contents.empty', $this->domain);

        if(ValidatorHelper::containsCodingCharacters($contents))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($contents) > 15000)
            throw new FrontEndException('course.edit.contents.too.long', $this->domain, array('%size%' => strlen($contents)));
    }

    public function validateYoutubeUrl($url)
    {
        if(!ValidatorHelper::isStringNullOrEmpty($url))
            if(!ValidatorHelper::isYoutubeUrl($url))
                throw new FrontEndException('course.edit.card.youtube.url', $this->domain);
    }
}