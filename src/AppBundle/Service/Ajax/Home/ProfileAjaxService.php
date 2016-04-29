<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 00:15
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Transformer\TransformManager;
use AppBundle\Util\FileHelper;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileAjaxService implements AjaxInterface
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
    /**
     * @var TransformManager
     */
    private $transformer;

    function __construct(EntityManager $manager, TokenStorage $storage, Validator $validator, TransformManager $transformer)
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->validator = $validator;
        $this->transformer = $transformer;
    }

    public function updatePerson($args)
    {
        $this->hasUserContext();

        $context = $args['context'];
        unset($args['context']);

        if($args['picture'] == '')
            $args['picture'] = null;

        $user = $this->manager->getRepository('AppBundle:User')->find($this->storage->getToken()->getUser()->getId());
        if($user == null) throw new EntityNotFoundException();

        $args['dateOfBirth'] = $this->tryCreateDate($args['dateOfBirth']);

        $this->validator->validate($args, 'Profile');

        //Not the same remove picture
        if($user->getPicture() != null && $user->getPicture() != '' && $user->getPicture() != $args['picture'])
        {
            $absolute = FileHelper::$webDir . $user->getPicture();

            unlink($absolute);
        }

        $user->setFirstName($args['firstName']);
        $user->setSurname($args['surname']);
        $user->setCountry($args['country']);
        $user->setDateOfBirth($args['dateOfBirth']);
        $user->setCustomEmail($args['email']);
        $user->setLanguage($args['language']);
        $user->setPicture($args['picture']);

        $this->manager->flush();

        $html = $this->transformer->getTransformerByName('ProfileTransformer')->transformToAjaxResponse(array($user), $context);
        return array('html' => $html);
    }

    public function updateEducation($args)
    {

    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //PRofile Ajax Service 1
        return 'PRAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('updatePerson', 'updateEducation');
    }

    private function hasUserContext()
    {
        if($this->storage->getToken() != null && $this->storage->getToken()->getUser() != null)
            return;
        throw new AccessDeniedException('No user context found');
    }

    private function tryCreateDate($date)
    {
        try
        {
            if($date == null || $date == '')
                return null;

            $realDate = \DateTime::createFromFormat('d-m-Y', $date);

            if($realDate === false)
                throw new \Exception();

            return $realDate;
        }
        catch(\Exception $e)
        {
            throw new FrontEndException('profile.edit.date.of.birth.invalid', 'ajaxerrors');
        }
    }
}