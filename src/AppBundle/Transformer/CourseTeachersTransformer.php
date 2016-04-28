<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 13-Mar-16
 * Time: 20:18
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class CourseTeachersTransformer implements TransformerInterface
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
     * Returns html for the given entity. It will use the context to determine
     * which layout should be used.
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        $html = '';
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach($entities as $entity)
            {
                $html .= $this->twig->render(':ajax/create-courses/teacher:card.teacher.row.html.twig', array('teacher' => $entity, 'modalId' => 'addTeacherModal'.$entity->getId(), 'index' => $entity->getId()));
                $html .= $this->twig->render(':modal/create-course:card.add.teacher.modal.html.twig', array('teacher' => $entity, 'modalId' => 'addTeacherModal'.$entity->getId()));
                $html .= $this->twig->render(':modal/create-course:card.remove.teacher.modal.html.twig', array('teacher' => $entity, 'modalId' => 'removeTeacherModal'.$entity->getId()));
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
        return 'CourseTeachersTransformer';
    }
}