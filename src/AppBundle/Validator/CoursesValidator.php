<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Feb-16
 * Time: 15:15
 */

namespace AppBundle\Validator;


use AppBundle\Exception\FrontEndException;
use AppBundle\Util\ValidatorHelper;
use Doctrine\ORM\EntityManager;

class CoursesValidator extends Validator
{
    private $unacceptableWordsFile;

    public function __construct(EntityManager $manager, $rootDir)
    {
        parent::__construct($manager, $rootDir);
        $this->unacceptableWordsFile = $rootDir.'/../src/AppBundle/Validator/Files/unacceptable.words.txt';
    }

    protected function validateLanguage($language)
    {
        if(ValidatorHelper::isStringNullOrEmpty($language))
            throw new FrontEndException('course.edit.language.required', $this->domain);


        $entity = $this->manager->getRepository('AppBundle:Course\CourseLanguages')->findOneBy(array('languageCode' => $language));
        if($entity == null)
            throw new FrontEndException('course.edit.language.not.found', $this->domain, array('%value%' => $language));
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

    protected function validateLevel($level)
    {
        if(!ValidatorHelper::isStringNullOrEmpty($level))
        {
            $entity = $this->manager->getRepository('AppBundle:Course\CourseLevels')->findOneBy(array('levelShort' => $level));
            if($entity == null)
                throw new FrontEndException('course.edit.level.not.found', $this->domain);
        }
    }

    protected function validateTags($tags)
    {
        foreach($tags as $tag)
        {
            if(ValidatorHelper::isStringNullOrEmpty($tag))
                throw new FrontEndException('course.edit.tag.empty', $this->domain);

            if(strlen($tag) > 100)
                throw new FrontEndException('course.edit.tag.too.long', $this->domain);

            if(ValidatorHelper::containsWordsInFile($tag, $this->unacceptableWordsFile))
                throw new FrontEndException('course.edit.tag.unacceptable.word', $this->domain, array('%tag%' => $tag));
        }
    }

    protected function validateEstimatedDuration($estimatedDuration)
    {
        if(!ValidatorHelper::containsOnlyNumbers($estimatedDuration))
            throw new FrontEndException('course.edit.estimated.duration.contains.characters', $this->domain);

        if(intval($estimatedDuration) > 10000)
            throw new FrontEndException('course.edit.estimated.duration.too.long', $this->domain);

        if(intval($estimatedDuration) <= 0)
            throw new FrontEndException('course.edit.estimated.duration.minus', $this->domain);
    }

    protected function validateDifficulty($difficulty)
    {
        if(!ValidatorHelper::containsOnlyNumbers($difficulty))
            throw new FrontEndException('course.edit.difficulty.contains.characters', $this->domain);

        if(intval($difficulty) > 5)
            throw new FrontEndException('course.edit.difficulty.too.long', $this->domain);

        if(intval($difficulty) < 0)
            throw new FrontEndException('course.edit.difficulty.minus', $this->domain);
    }

    protected function validateDescription($description)
    {
        if(ValidatorHelper::containsCodingCharacters($description))
            throw new FrontEndException('course.edit.description.contains.coding', $this->domain);

        if(strlen($description) > 15000)
            throw new FrontEndException('course.edit.description.too.long', $this->domain);
    }

    protected function validateCategory($category)
    {
        if(ValidatorHelper::isStringNullOrEmpty($category))
            throw new FrontEndException('course.edit.category.empty');
    }
}