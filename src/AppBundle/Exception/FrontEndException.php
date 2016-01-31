<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 01-Dec-15
 * Time: 22:30
 */

namespace AppBundle\Exception;


class FrontEndException extends \Exception
{
    private $translationCode;
    private $params;

    /**
     * TranslatableException constructor.
     *
     * @param string $translationCode
     * @param array  $params
     */
    public function __construct($translationCode, array $params = array())
    {
        $this->params = $params;
        $this->translationCode = $translationCode;
    }

    /**
     * @return string
     */
    public function getTranslationCode()
    {
        return $this->translationCode;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}