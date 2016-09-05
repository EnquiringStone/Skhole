<?php
/**
 * Created by PhpStorm.
 * User: EnquiringStone
 * Date: 04-Sep-16
 * Time: 18:07
 */

namespace AppBundle\Exception;

class DelayException extends \Exception
{
    private $delayCount;

    public function setDelayCount($delayCount)
    {
        $this->delayCount = $delayCount;
    }

    public function getDelayCount()
    {
        return $this->delayCount;
    }
}