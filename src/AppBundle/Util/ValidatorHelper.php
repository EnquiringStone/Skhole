<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 27-Feb-16
 * Time: 15:18
 */

namespace AppBundle\Util;


abstract class ValidatorHelper
{
    public static function isStringNullOrEmpty($string)
    {
        if(!empty($string))
            return $string == null || $string == '';

        return true;
    }

    public static function containsOnlyNumbers($string)
    {
        return preg_match('/^[0-9]+$/', $string);
    }

    public static function containsOnlyCharacters($string)
    {
        return preg_match('/^[a-zA-Z\\s]+$/', $string);
    }

    public static function containsOnlyCharactersAndNumbers($string)
    {
        return preg_match('/^[\'%-.,a-zA-Z0-9\\s]+$/', $string);
    }

    public static function containsWordsInFile($word, $file)
    {
        $wordsFile = file($file);
        $words = array();

        foreach($wordsFile as $item)
        {
            $words[] = preg_replace('/\s+/', ' ', trim($item));
        }
        $words = implode('|', $words);

        if(preg_match('/'.$words.'/', $word)) return true;
        return false;
    }

    public static function containsCodingCharacters($string)
    {
        return preg_match('/<|>/', $string);
    }

    public static function isYoutubeUrl($string)
    {
        return preg_match('/(?:https?:\/\/)?(?:youtu\.be\/|(?:www\.)?youtube\.com\/watch(?:\.php)?\?.*v=)([a-zA-Z0-9\-_]+)/', $string);
    }

    public static function isValidEmail($email)
    {
        return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $email);
    }

    public static function isGoogleDriveUrl($string)
    {
        return self::isValidUrl($string) && strpos($string, 'drive') !== FALSE && strpos($string, 'google') !== FALSE && strpos($string, 'usp=sharing') !== FALSE;
    }

    public static function isDropboxUrl($string)
    {
        return self::isValidUrl($string) && strpos($string, 'dropbox') !== FALSE;
    }

    public static function isValidUrl($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== FALSE;
    }
}