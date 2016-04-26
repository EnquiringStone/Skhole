<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 16:46
 */

namespace AppBundle\Transformer;


use AppBundle\Interfaces\TransformerInterface;

class CourseQuestionsTransformer implements TransformerInterface
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * CourseQuestionsTransformer constructor.
     *
     * @param \Twig_Environment $environment
     */
    public function __construct(\Twig_Environment $environment)
    {

        $this->environment = $environment;
    }

    /**
     * Flattens the entity object to an array.
     *
     * @param $entities
     * @return mixed
     */
    function transformToAjaxResponse($entities)
    {
        return $this->environment->render(':ajax/create-courses/questions:question.row.html.twig', array('questions' => $entities));
    }

    /**
     * Returns the name. Should be the same as the class name
     *
     * @return string
     */
    function getName()
    {
        return 'CourseQuestionsTransformer';
    }
}