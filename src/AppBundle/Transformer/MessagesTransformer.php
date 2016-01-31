<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 22:35
 */

namespace AppBundle\Transformer;


use AppBundle\Entity\Messages;
use AppBundle\Interfaces\TransformerInterface;
use Twig_Extensions_Extension_Date;

class MessagesTransformer implements TransformerInterface
{
    /**
     * @var Twig_Extensions_Extension_Date
     */
    private $extensionDate;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var
     */
    private $dateFormat;

    public function __construct(Twig_Extensions_Extension_Date $extensionDate, \Twig_Environment $environment, $dateFormat)
    {
        $this->extensionDate = $extensionDate;
        $this->environment = $environment;
        $this->dateFormat = $dateFormat;
    }

    /**
     * Flattens the entity object to an array.
     *
     * @param $entities
     * @return mixed
     */
    public function transformToAjaxResponse($entities)
    {
        $ajaxArray = array();
        foreach($entities as $entity) {
            if($entity instanceof Messages) {
                $timeBetween = $this->extensionDate->diff($this->environment, $entity->getSendDateTime());
                $ajaxArray[] = ['id' => $entity->getId(), 'title' => $entity->getTitle(),
                    'contents' => $entity->getContents(), 'isRead' => $entity->getIsRead(),
                    'sender' => $entity->getSenderName(), 'sendDate' => $entity->getSendDateTime()->format($this->dateFormat),
                    'timeBetween' => $timeBetween];
            }
        }
        return $ajaxArray;
    }


    /**
     * Returns the name. Should be the same as the class name
     *
     * @return string
     */
    function getName()
    {
        return 'MessagesTransformer';
    }
}