<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 19-Mar-16
 * Time: 14:53
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class CourseAnnouncementsTransformer implements TransformerInterface
{

    /**
     * @var \Twig_Environment
     */
    private $environment;

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
        $html = '';
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach($entities as $entity)
            {
                $i = $entity->getId();
                $html .= $this->environment->render(':ajax/create-courses/announcement:announcement.row.html.twig', array('announcement' => $entity, 'modalId' => 'courseAnnouncementModal'.$i, 'index' => $i));
                $html .= $this->environment->render(':modal/create-course:course.announcement.modal.html.twig', array('announcement' => $entity, 'modalId' => 'courseAnnouncementModal'.$i));
                $html .= $this->environment->render(':modal/create-course:course.remove.announcement.modal.html.twig', array('announcement' => $entity, 'modalId' => 'removeCourseAnnouncementModal'.$i));
            }
        }
        elseif(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::PUBLIC_CONTEXT, $context))
        {
            foreach($entities as $entity)
            {
                $html .= $this->environment->render(':ajax/study:course.announcement.row.html.twig', array('announcement' => $entity));
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
        return 'CourseAnnouncementsTransformer';
    }
}