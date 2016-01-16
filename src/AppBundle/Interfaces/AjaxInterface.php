<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Dec-15
 * Time: 19:40
 */
namespace AppBundle\Interfaces;

interface AjaxInterface
{
    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     * @return string
     */
    public function getUniqueCode();

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     * @return array
     */
    public function getSubscribedMethods();
}