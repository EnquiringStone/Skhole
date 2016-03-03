<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Feb-16
 * Time: 19:50
 */

namespace AppBundle\Service\Ajax\Teach;


use AppBundle\Entity\Course\CourseCards;
use AppBundle\Entity\Course\CourseSchedules;
use AppBundle\Entity\Tags;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Util\SecurityHelper;
use AppBundle\Util\TransformerHelper;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreateCourseAjaxService implements AjaxInterface
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var TokenStorage
     */
    private $storage;
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(EntityManager $manager, TokenStorage $storage, Validator $validator)
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->validator = $validator;
    }

    public function saveCourseInformationValues($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);

        unset($args['id']);

        $stringTags = explode(',', $args['tags']);
        $existingTags = array();
        $checkTags = array();
        foreach($stringTags as $stringTag)
        {
            $tagEntity = $this->manager->getRepository('AppBundle:Tags')->findOneBy(array('tag' => strtolower($stringTag)));
            if($tagEntity != null)
            {
                $existingTags[$tagEntity->getId()] = $tagEntity;
            } else
            {
                $checkTags[] = $stringTag;
            }
        }
        $args['tags'] = $checkTags;

        if($args['level'] == '')
        {
            if($entity->getLevel() != null)
                $entity->setLevel(null);

            unset($args['level']);
        }

        $this->validator->validate($args, 'courses');

        foreach($existingTags as $tag)
        {
            if(!$entity->getTags()->contains($tag))
            {
                $entity->addTag($tag);
            }
        }
        foreach($checkTags as $tag)
        {
            $object = new Tags();
            $object->setTag(strtolower($tag));
            $entity->addTag($object);

            $this->manager->persist($object);
        }

        $entity->setName($args['name']);
        $entity->setDescription($args['description']);
        $entity->setLanguage($this->manager->getRepository('AppBundle:Course\CourseLanguages')->findOneBy(array('languageCode' => $args['language'])));
        if(array_key_exists('level', $args))
            $entity->setLevel($this->manager->getRepository('AppBundle:Course\CourseLevels')->findOneBy(array('levelShort' => $args['level'])));
        $entity->setDifficulty(intval($args['difficulty']));
        $entity->setEstimatedDuration(intval($args['estimatedDuration']));

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    public function saveCardIntroduction($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        if($args['description'] == '' && $args['youtubeUrl'] == '')
            throw new FrontEndException('course.edit.card.youtube.url.or.description', 'ajaxerrors');

        if($args['youtubeUrl'] == '')
            unset($args['youtubeUrl']);

        $this->validator->validate($args, 'CourseCards');

        $card = $entity->getCourseCard();
        if($card == null) {
            $card = new CourseCards();
            $card->setCourse($entity);
        }

        $card->setDescription($args['description']);
        $card->setName($args['name']);
        if(array_key_exists('youtubeUrl', $args))
        {
            $card->setYoutubeUrl($args['youtubeUrl']);
            $card->setYoutubeEmbedUrl(TransformerHelper::createEmbeddedYoutubeUrl($args['youtubeUrl']));
        }
        else
        {
            $card->setYoutubeUrl(null);
            $card->setYoutubeEmbedUrl(null);
        }

        $this->manager->persist($card);
        $this->manager->flush();
    }

    public function saveCardProvider($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseCards');

        $card = $entity->getCourseCard();
        if($card == null)
        {
            $card = new CourseCards();
            $card->setCourse($entity);
        }

        $card->setPriorKnowledge($args['priorKnowledge']);

        $this->manager->persist($card);
        $this->manager->flush();
    }

    public function saveSchedule($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        $this->validator->validate($args, 'CourseSchedules');

        $schedule = $entity->getCourseSchedule();
        if($schedule == null)
        {
            $schedule = new CourseSchedules();
            $schedule->setCourse($entity);
            $schedule->setIsUndesirable(false);
        }
        $schedule->setSchedule($args['schedule']);

        $this->manager->persist($schedule);
        $this->manager->flush();
    }


    public function saveTeacher($args)
    {
        $this->idSpecified($args);
        $entity = $this->manager->getRepository('AppBundle:Course\Courses')->find($args['id']);
        if($entity == null) throw new EntityNotFoundException();
        $this->hasEditRights($entity);
        unset($args['id']);

        print_r($args['picture']); exit;

        $this->validator->validate($args, 'CourseTeachers');
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Create Course Ajax Service
        return 'CCAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('saveCourseInformationValues', 'saveCardIntroduction', 'saveCardProvider', 'saveSchedule');
    }

    private function idSpecified($args)
    {
        if(!array_key_exists('id', $args))
            throw new \Exception('Id should be specified');
    }

    private function hasEditRights($entity)
    {
        if(SecurityHelper::hasUserContext($this->storage) && SecurityHelper::hasEditRights($entity, 'userInsertedId', $this->storage->getToken()->getUser()->getId()))
            return;

        throw new AccessDeniedException();
    }
}