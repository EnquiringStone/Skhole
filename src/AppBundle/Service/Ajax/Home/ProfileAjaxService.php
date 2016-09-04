<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 00:15
 */

namespace AppBundle\Service\Ajax\Home;


use AppBundle\Authorization\AuthorizationService;
use AppBundle\Entity\Education\Educations;
use AppBundle\Exception\FrontEndException;
use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Transformer\TransformManager;
use AppBundle\Util\FileHelper;
use AppBundle\Util\ValidatorHelper;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityManager;

class ProfileAjaxService implements AjaxInterface
{

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var TransformManager
     */
    private $transformer;
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    function __construct(EntityManager $manager, Validator $validator, TransformManager $transformer, AuthorizationService $authorizationService)
    {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->transformer = $transformer;
        $this->authorizationService = $authorizationService;
    }

    public function updatePerson($args)
    {
        $user = $this->hasUserContext();

        $context = $args['context'];
        unset($args['context']);

        if($args['picture'] == '')
            $args['picture'] = null;

        $args['dateOfBirth'] = $this->tryCreateDate($args['dateOfBirth']);

        $this->validator->validate($args, 'Profile');

        //Not the same remove picture
        if($user->getPicture() != null && $user->getPicture() != '' && $user->getPicture() != $args['picture'])
        {
            $fileHelper = new FileHelper();

            $absolute = $fileHelper->getWebDir() . $user->getPicture();

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
        $user = $this->hasUserContext();

        $context = $args['context'];
        unset($args['context']);

        $this->validator->validate($args, 'EducationProfile');

        $education = $this->manager->getRepository('AppBundle:Education\Educations')->findOneBy(array('userId' => $user->getId()));
        if($education == null)
        {
            $education = new Educations();
            $education->setUser($user);
            $this->manager->persist($education);
        }
        $education->setClass($args['class'] == '' ? null : $args['class']);
        $education->setLevel($args['level'] == '' ? null : $args['level']);
        $education->setSchoolYear($args['schoolYear'] == '' ? null : intval($args['schoolYear']));

        $this->manager->flush();

        $html = $this->transformer->getTransformerByName('ProfileEducationTransformer')->transformToAjaxResponse(array($education), $context);
        return array('html' => $html);
    }

    public function removeProfilePicture()
    {
        $user = $this->hasUserContext();
        $picture = $user->getPicture();

        if (ValidatorHelper::isStringNullOrEmpty($picture)) return;

        $user->setPicture(null);
        $this->manager->flush();

        $fileHelper = new FileHelper();
        $absolute = $fileHelper->getWebDir() . $picture;
        if (is_file($absolute))
            unlink($absolute);
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
        return array('updatePerson', 'updateEducation', 'removeProfilePicture');
    }

    /**
     * @return \AppBundle\Entity\User
     */
    private function hasUserContext()
    {
        return $this->authorizationService->getAuthorizedUserOrThrowException();
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