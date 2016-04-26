<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Mar-16
 * Time: 17:18
 */

namespace AppBundle\Twig;


class OrderExtension extends \Twig_Extension
{
    private $alphabet = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    );

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('orderToAlphabet', array($this, 'orderToAlphabet'))
        );
    }

    public function orderToAlphabet($number, $toUpper = false)
    {
        if($number == 0)
            $number = 1;

        if(($number - 1) > sizeof($this->alphabet))
            $number = (($number - 1) % sizeof($this->alphabet)) + 1;

        return $toUpper ? strtoupper($this->alphabet[$number - 1]) : $this->alphabet[$number - 1];
    }

    public function getName()
    {
        return 'app_order_extension';
    }
}