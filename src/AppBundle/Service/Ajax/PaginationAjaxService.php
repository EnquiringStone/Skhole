<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Jan-16
 * Time: 00:01
 */

namespace AppBundle\Service\Ajax;


use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Interfaces\PaginationInterface;
use AppBundle\Interfaces\TransformerInterface;
use AppBundle\Transformer\TransformManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PaginationAjaxService implements AjaxInterface
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
     * @var
     */
    private $limit;

    /**
     * @var TransformManager
     */
    private $transformManager;
    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(EntityManager $manager, TokenStorage $storage, TransformManager $transformManager, \Twig_Environment $environment, $limit)
    {

        $this->manager = $manager;
        $this->storage = $storage;
        $this->limit = $limit;
        $this->transformManager = $transformManager;
        $this->environment = $environment;
    }

    public function paginate($args)
    {
        if(array_key_exists('entity', $args) && array_key_exists('offset', $args))
        {
            $hasOrder = false;
            if(array_key_exists('attribute', $args) && array_key_exists('order', $args))
                $hasOrder = true;

            $object = 'AppBundle\\Entity\\' . $args['entity'];
            if($this->hasUserContext() && class_exists($object))
            {
                $repository = $this->manager->getRepository($object);
                $userId = $this->storage->getToken()->getUser()->getId();
                //TODO throw exception, translating
                if($repository instanceof PaginationInterface)
                {
                    $resultSet = $repository->getByPage($args['offset'], $this->limit, $hasOrder ? $args['attribute'] : null,
                        $hasOrder ? $args['order'] : null, $userId);
                    $entityData = explode('\\', $args['entity']);

                    $object = 'AppBundle\\Transformer\\' . end($entityData) . 'Transformer';

                    if(class_exists($object))
                    {
                        $transformer = $this->transformManager->getTransformerByName(end($entityData) . 'Transformer');
                        if($transformer != null && $transformer instanceof TransformerInterface)
                            return $transformer->transformToAjaxResponse($resultSet);
                    }
                }
            }
        }
    }

    public function reRenderPagination($args)
    {
        if(!array_key_exists('offset', $args) || !array_key_exists('maxPages', $args) || !array_key_exists('entity', $args))
            throw new \Exception('Not all arguments specified');

        $object = 'AppBundle\\Entity\\' . $args['entity'];
        if($this->hasUserContext() && class_exists($object))
        {
            $repository = $this->manager->getRepository($object);
            if($repository instanceof PaginationInterface)
            {
                $count = $repository->getPaginationCount($this->storage->getToken()->getUser()->getId());
                return array('html' => $this->environment->render(':elements:pagination.html.twig', array('maxPages' => $args['maxPages'],
                    'offset' => $args['offset'], 'limit' => $this->limit, 'maxEntities' => $count,
                    'entity' => $args['entity'], 'pageName' => $args['pageName'])));
            }
        }
        throw new \Exception('wrong');
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Pagination Ajax Service 1
        return 'PAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('paginate',
            'reRenderPagination');
    }

    private function hasUserContext()
    {
        return $this->storage->getToken() != null && $this->storage->getToken()->getUser() != null;
    }
}