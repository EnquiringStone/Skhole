<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 29-Apr-16
 * Time: 21:16
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class CourseCollectionItemsTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $environment;

    function __construct(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     *
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        $html = '';
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            $index = 1;
            foreach($entities as $entity)
            {
                $html .= $this->environment->render(':ajax/course-collection:course.collection.item.row.html.twig', array('course' => $entity->getCourse(), 'index' => $index));
                $html .= $this->environment->render(':modal/course-collection:remove.collection.item.modal.html.twig', array('collectionItem' => $entity, 'modalId' => 'removeCollectionItem'.$index));
                $html .= $this->environment->render(':modal/learn:course.card.details.modal.html.twig', array('course' => $entity->getCourse(), 'modalId' => 'courseCardDetailsModal'.$index));
                $index ++;
            }
        }
        return $html;
    }

    /**
     * Returns the name. Should be the same as the class name
     *
     * @return string
     */
    function getName()
    {
        return 'CourseCollectionItemsTransformer';
    }
}