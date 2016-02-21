<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-Feb-16
 * Time: 18:33
 */

namespace AppBundle\Transformer;


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
     * Flattens the entity object to an array.
     *
     * @param $entities
     * @return mixed
     */
    function transformToAjaxResponse($entities)
    {
        $html = '';
        $index = 1;
        foreach($entities as $entity)
        {
            $html .= $this->twig->render('ajax/my-courses/course.detail.body.html.twig', array('course' => $entity, 'index' => $index));
            $html .= $this->twig->render(':modal/my-courses:course.details.modal.html.twig', array('course' => $entity, 'modalId' => 'courseDetailsModal'.$index));
            $html .= $this->twig->render(':modal/my-courses:course.remove.modal.html.twig', array('course' => $entity, 'modalId' => 'courseRemoveModal'.$index));
            $index ++;
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