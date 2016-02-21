<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-Feb-16
 * Time: 18:13
 */

namespace AppBundle\Transformer;


use AppBundle\Interfaces\TransformerInterface;

class CourseReviewsTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Flattens the entity object to an array.
     *
     * @param $entities
     * @return mixed
     */
    function transformToAjaxResponse($entities)
    {
        $html = "";
        foreach($entities as $entity)
        {
            $html .= $this->twig->render(':ajax/my-courses:course.review.body.html.twig', array('review' => $entity));
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
        return 'CourseReviewsTransformer';
    }
}