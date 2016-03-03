<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Feb-16
 * Time: 16:49
 */

namespace AppBundle\Validator;


use Doctrine\ORM\EntityManager;

class Validator
{
    /**
     * @var EntityManager
     */
    protected $manager;
    protected $rootDir;
    protected $domain = 'ajaxerrors';

    public function __construct(EntityManager $manager, $rootDir)
    {
        $this->manager = $manager;
        $this->rootDir = $rootDir;

    }

    /**
     * Validates all attributes and their values. {key: value, key: value} where
     * the key is the attribute and the value its value.
     *
     * The entity should be the same as the entity specified in AppBundle\Entities
     *
     * The params are extra parameters passed to the validator class
     *
     * @param array $attributes
     * @param string $entity
     * @param array $params
     */
    public function validate($attributes, $entity, $params = array())
    {
        $object = 'AppBundle\Validator\\'.ucfirst($entity).'Validator';

        $initializedObject = new $object($this->manager, $this->rootDir, $params);

        foreach($attributes as $attribute => $value)
        {
            $method = 'validate'.ucfirst($attribute);
            $initializedObject->$method($value);
        }
    }
}