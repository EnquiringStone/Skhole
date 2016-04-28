<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Apr-16
 * Time: 16:46
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
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
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            return $this->environment->render(':ajax/create-courses/questions:question.row.html.twig', array('questions' => $entities));
        }
        return '';
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