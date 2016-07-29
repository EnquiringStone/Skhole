<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 29-Jul-16
 * Time: 14:32
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class ReportsTransformer implements TransformerInterface
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
     * @param $entities
     * @param $context
     * @return mixed
     */
    function transformToAjaxResponse($entities, $context)
    {
        $html = '';
        $index = 1;
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context) || ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::ANONYMOUS_CONTEXT, $context))
        {
            foreach ($entities as $entity)
            {
                $html .= $this->environment->render(':ajax/reports:report.detail.body.html.twig', array('report' => $entity, 'index' => $index));
                $html .= $this->environment->render(':modal/learn:report.share.modal.html.twig', array('report' => $entity, 'modalId' => 'shareReportModal'.$index));
                $html .= $this->environment->render(':modal/learn:report.remove.modal.html.twig', array('report' => $entity, 'modalId' => 'removeReportModal'.$index));
                $html .= $this->environment->render(':modal/learn:course.card.details.modal.html.twig', array('course' => $entity->getCourse(), 'modalId' => 'courseCardModal'.$index));

                $index ++;
            }
        }

        return $html;
    }

    /**
     * Returns the name. Should be the same as the class name
     * @return string
     */
    function getName()
    {
        return 'ReportsTransformer';
    }
}