<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 30-Jul-16
 * Time: 15:49
 */

namespace AppBundle\Transformer;


use AppBundle\Enum\ContextEnum;
use AppBundle\Interfaces\TransformerInterface;

class SharedReportsTransformer implements TransformerInterface
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
        if(ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::REQUEST_CONTEXT, $context))
        {
            foreach ($entities as $entity)
            {
                $html .= $this->environment->render('ajax/teach/mentor.request.row.html.twig', array('request' => $entity, 'index' => $index));
                $html .= $this->environment->render(':modal/teach:education.profile.modal.html.twig', array('user' => $entity->getUser(), 'modalId' => 'educationProfileModal'.$index));
                $index ++;
            }
        }
        elseif (ContextEnum::matchValueWithGivenEnum(ContextEnum::class, ContextEnum::SELF_CONTEXT, $context))
        {
            foreach ($entities as $entity)
            {
                $html .= $this->environment->render(':ajax/teach:shared.reports.table.row.html.twig', array('report' => $entity, 'index' => $index));
                $html .= $this->environment->render(':modal/teach:remove.report.modal.html.twig', array('report' => $entity, 'modalId' => 'removeReportModal'.$index));
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
        return 'SharedReportsTransformer';
    }
}