<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 26-Jan-16
 * Time: 22:18
 */

namespace AppBundle\Service\Ajax;


use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Interfaces\SortableInterface;
use AppBundle\Interfaces\TransformerInterface;
use AppBundle\Transformer\TransformManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SortAjaxService implements AjaxInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var
     */
    private $limit;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var TransformManager
     */
    private $transformManager;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, TransformManager $transformManager, $limit)
    {
        $this->entityManager = $entityManager;
        $this->limit = $limit;
        $this->tokenStorage = $tokenStorage;
        $this->transformManager = $transformManager;
    }

    public function sortByAttribute(array $args)
    {
        if(!key_exists('entity', $args) || !key_exists('attribute', $args) || !key_exists('order', $args))
            throw new \Exception('Key not defined');

        if($this->tokenStorage->getToken() == null || $this->tokenStorage->getToken()->getUser() == null)
            throw new AccessDeniedException('Can\'t sort when not logged in');

        $object = 'AppBundle\Entity\\' . $args['entity'];

        if(class_exists($object))
        {
            $repository = $this->entityManager->getRepository($object);

            if($repository instanceof SortableInterface)
            {
                $resultSet = $repository->sortByAttribute($args['attribute'], $args['order'], $args['offset'],
                    $this->limit, $this->tokenStorage->getToken()->getUser()->getId());

                if($resultSet == null)
                    throw new \Exception('no data found when sorting');

                $entityData = explode('\\', $args['entity']);

                $object = 'AppBundle\\Transformer\\' . end($entityData) . 'Transformer';

                if(class_exists($object))
                {
                    $transformer = $this->transformManager->getTransformerByName(end($entityData) . 'Transformer');
                    if($transformer != null && $transformer instanceof TransformerInterface)
                        return $transformer->transformToAjaxResponse($resultSet);
                }
                throw new \Exception('object has no transformer configured');
            }
            throw new \Exception('object is not sortable');
        }
        throw new \Exception('object is not a class');
        //TODO throw frontend exception with translations
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Sort Ajax Service 1
        return 'SAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array(
            'sortByAttribute'
        );
    }
}