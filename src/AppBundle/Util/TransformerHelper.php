<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Feb-16
 * Time: 15:22
 */

namespace AppBundle\Util;


abstract class TransformerHelper
{
    public static function createEmbeddedYoutubeUrl($url)
    {
        $embedUrl = 'https://www.youtube.com/embed/';

        if(!ValidatorHelper::isYoutubeUrl($url))
            throw new \Exception('Only youtube urls are supported');

        if(preg_match('/watch/', $url))
        {
            //https://www.youtube.com/watch?v=ftLIR5AjqMs
            $parts = explode('?v=', $url);

            $url = $parts[sizeof($parts) - 1];
        }
        else
        {
            //https://youtu.be/ftLIR5AjqMs
            $parts = explode('/', $url);

            $url = $parts[sizeof($parts) - 1];
        }

        return $embedUrl.$url;
    }
}