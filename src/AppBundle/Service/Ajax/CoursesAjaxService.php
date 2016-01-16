<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 02-Dec-15
 * Time: 19:46
 */

namespace AppBundle\Service\Ajax;


use AppBundle\Interfaces\AjaxInterface;

class CoursesAjaxService implements AjaxInterface
{

    public function __construct()
    {
    }


    public function Validate(array $params = null)
    {
        print_r('jeej');
        print_r($params);
        exit;
    }

    /**
     * Returns an unique code that is used to determine which implementation
     * of this interface should be used for the ajax call
     *
     * @return string
     */
    public function getUniqueCode()
    {
        //Course(S) Ajax Service 1
        return 'CSAS1';
    }

    /**
     * Returns a list of public methods defined within its class that
     * can be accessed from an ajax call
     *
     * @return array
     */
    public function getSubscribedMethods()
    {
        return array('Validate');
    }
}