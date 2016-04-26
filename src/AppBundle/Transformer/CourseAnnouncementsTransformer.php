<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 19-Mar-16
 * Time: 14:53
 */

namespace AppBundle\Transformer;


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
     * Flattens the entity object to an array.
     *
     * @param $entities
     * @return mixed
     */
    function transformToAjaxResponse($entities)
    {
        $html = '';
        foreach($entities as $entity)
        {
            $i = $entity->getId();
            $html .= $this->environment->render(':ajax/create-courses/announcement:announcement.row.html.twig', array('announcement' => $entity, 'modalId' => 'courseAnnouncementModal'.$i, 'index' => $i));
            $html .= $this->environment->render(':modal/create-course:course.announcement.modal.html.twig', array('announcement' => $entity, 'modalId' => 'courseAnnouncementModal'.$i));
            $html .= $this->environment->render(':modal/create-course:course.remove.announcement.modal.html.twig', array('announcement' => $entity, 'modalId' => 'removeCourseAnnouncementModal'.$i));
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