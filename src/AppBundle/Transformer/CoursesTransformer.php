<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-Feb-16
 * Time: 18:33
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class CoursesTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var
     */
    private $limit;
    /**
     * @var
     */
    private $maxPages;

    public function __construct(\Twig_Environment $twig, $limit, $maxPages)
    {
        $this->twig = $twig;
        $this->limit = $limit;
        $this->maxPages = $maxPages;
    }

    /**
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        $html = '';
        $index = 1;
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach ($entities as $entity)
            {
                $html .= $this->twig->render('ajax/my-courses/course.detail.body.html.twig', array('course' => $entity, 'index' => $index));
                $html .= $this->twig->render(':modal/my-courses:course.details.modal.html.twig', array('course' => $entity, 'modalId' => 'courseDetailsModal' . $index));
                $html .= $this->twig->render(':modal/my-courses:course.remove.modal.html.twig', array('course' => $entity, 'modalId' => 'courseRemoveModal' . $index));
                $index++;
            }
        }
        elseif(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SEARCH_CONTEXT, $context))
        {
            foreach($entities as $entity)
            {
                $html .= $this->twig->render(':ajax/search:course.details.row.html.twig', array('course' => $entity, 'index' => $index));
                $html .= $this->twig->render(':modal/learn:course.card.details.modal.html.twig', array('course' => $entity, 'modalId' => 'courseCardDetailsModal'.$index));
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
        return 'CoursesTransformer';
    }
}