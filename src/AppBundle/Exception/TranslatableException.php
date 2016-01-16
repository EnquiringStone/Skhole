<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 01-Dec-15
 * Time: 22:30
 */

namespace AppBundle\Exception;


class TranslatableException extends \Exception
{
    private $translationCode;
    private $params;
    private $locale;

    /**
     * TranslatableException constructor.
     *
     * @param string $translationCode
     * @param string    $locale
     * @param array  $params
     */
    public function __construct($translationCode, $locale, array $params = array())
    {
        $this->params = $params;
        $this->translationCode = $translationCode;
        $this->locale = $locale;
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
    public function getLocale()
    {
        return $this->locale;
    }
}