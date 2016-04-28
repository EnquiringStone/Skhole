<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 13-Feb-16
 * Time: 18:54
 */

namespace AppBundle\Service\Ajax;


use AppBundle\Interfaces\AjaxInterface;
use AppBundle\Interfaces\PageControlsInterface;
use AppBundle\Interfaces\TransformerInterface;
use AppBundle\Transformer\TransformManager;
use AppBundle\Util\PageControlHelper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class PageControlsAjaxService implements AjaxInterface
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
     * @var TransformManager
     */
    private $transformer;
    /**
     * @var int
     */
    private $defaultLimit;
    /**
     * @var int
     */
    private $defaultPagination;
    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(EntityManager $manager, TokenStorage $storage, TransformManager $transformer, \Twig_Environment $environment, $defaultLimit, $defaultPagination)
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->transformer = $transformer;
        $this->defaultLimit = $defaultLimit;
        $this->defaultPagination = $defaultPagination;
        $this->environment = $environment;
    }

    public function update($args)
    {
        $this->validate($args);
        if(!array_key_exists('limit', $args)) $args['limit'] = $this->defaultLimit;
        if(!array_key_exists('offset', $args)) $args['offset'] = 0;
        if(!array_key_exists('searchValues', $args)) $args['searchValues'] = array();
        else $args['searchValues'] = PageControlHelper::createDefaultSearch($args['searchValues']);

        $object = 'AppBundle\Entity\\' . $args['entity'];
        if(class_exists($object))
        {
            $entity = $this->manager->getRepository($object);
            if($entity instanceof PageControlsInterface)
            {
                if(array_key_exists('search', $args) && $entity->hasSearch())
                {
                    $searchParams = array('defaultSearch' => $args['searchValues'], 'searchQuery' => $args['search'], 'correlationType' => $args['correlation']);
                    $data = $entity->getRecordsBySearch($args['offset'], $args['limit'], $this->createSort($args),
                        $searchParams, $args['context'] == 'SELF' ? $this->storage->getToken()->getUser()->getId() : 0);
                }
                else
                {
                    $data = $entity->getRecords($args['searchValues'], $args['offset'], $args['limit'], $this->createSort($args),
                        $args['context'] == 'SELF' ? $this->storage->getToken()->getUser()->getId() : 0);
                }
                $transformer = $this->getTransformerByEntity($args['entity']);

                $entitiesHtml = $transformer->transformToAjaxResponse($data['resultSet'], $args['context']);

                $paginationHtml = $entity->hasPagination() ? $this->environment->render(':elements:pagination.html.twig', array('maxPages' => $this->defaultPagination,
                    'offset' => $args['offset'], 'limit' => $args['limit'], 'maxEntities' => $data['total'],
                    'entity' => $args['entity'], 'pageName' => $args['pageName'])) : null;

                $searchHtml = null;
                $sortHtml = null;

                if(array_key_exists('view', $args))
                {
                    $viewObject = ':ajax/'.$args['view'].':';

                    $sort = $this->createSort($args);
                    if($sort == null)
                    {
                        $sortHtml = $entity->hasSort() ? $this->environment->render($viewObject.'sort.html.twig') : null;
                    }
                    else
                    {
                        $sortHtml = $entity->hasSort()
                            ? $this->environment->render($viewObject.'sort.html.twig',
                                array('attribute' => $sort['sortAttribute'], 'value' => $sort['sortValue']))
                            : null;
                    }
                }
                return array('entitiesHtml' => $entitiesHtml, 'paginationHtml' => $paginationHtml, 'sortHtml' => $sortHtml, 'totalFound' => $data['total']);
            }
            throw new \Exception('entity '.$object.' not instance of page controls');
        }
        throw new \Exception('Given entity '.$args['entity'].' is not valid');
    }

    private function getTransformerByEntity($entity)
    {
        $entityData = explode('\\', $entity);

        $object = 'AppBundle\\Transformer\\' . end($entityData) . 'Transformer';

        if(class_exists($object))
        {
            $transformer = $this->transformer->getTransformerByName(end($entityData) . 'Transformer');
            if($transformer != null && $transformer instanceof TransformerInterface)
                return $transformer;
            throw new \Exception('Transformer not found for '.$entity);
        }
        throw new \Exception('object not instance of class '.$object);
    }

    private function createSort($args)
    {
        if(array_key_exists('sortAttribute',$args) && array_key_exists('sortValue', $args))
            return array('sortAttribute' => $args['sortAttribute'], 'sortValue' => $args['sortValue']);
        return null;
    }

    private function validate($args)
    {
        if(!array_key_exists('context', $args) || !array_key_exists('entity', $args) || !array_key_exists('pageName', $args))
            throw new \Exception('Context, entity and page name fields needs to be configured');

        if($args['context'] == 'SELF' && ($this->storage->getToken() == null || $this->storage->getToken()->getUser() == null))
            throw new \Exception('context is self but no user context found');
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Page Controls Ajax Service
        return 'PCAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('update');
    }
}