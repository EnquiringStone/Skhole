<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Jan-16
 * Time: 23:14
 */

namespace AppBundle\Transformer;


use AppBundle\Entity\Messages;
use AppBundle\Interfaces\TransformerInterface;

class TransformManager
{
    private $transformers = array();

    public function __construct()
    {
        foreach(func_get_args() as $arg)
        {
            if($arg instanceof TransformerInterface)
                $this->transformers[$arg->getName()] = $arg;
        }
    }

    /**
     * @param $name
     * @return TransformerInterface | null
     */
    public function getTransformerByName($name)
    {
        return array_key_exists($name, $this->transformers) ? $this->transformers[$name] : null;
    }

    public function getTransformerByEntity($entity)
    {
        if($entity instanceof Messages)
            return $this->transformers['MessagesTransformer'];
    }
}