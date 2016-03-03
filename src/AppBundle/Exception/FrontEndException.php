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
    private $translationDomain;

    /**
     * TranslatableException constructor.
     *
     * @param string $translationCode
     * @param array  $params
     * @param string $translationDomain
     */
    public function __construct($translationCode, $translationDomain = '', array $params = array())
    {
        $this->params = $params;
        $this->translationCode = $translationCode;
        $this->translationDomain = $translationDomain;
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

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }
}