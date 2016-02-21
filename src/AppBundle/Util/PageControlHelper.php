<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 20-Feb-16
 * Time: 19:09
 */

namespace AppBundle\Util;


class PageControlHelper
{

    public static function createSearch($searchAttributes)
    {
        $search = array();
        $value = $searchAttributes['value'];

        unset($searchAttributes['value']);

        foreach($searchAttributes as $entity => $element)
        {
            $attributes = explode(',', $element);

            $data = array();
            foreach($attributes as $attribute)
            {
                $data[$attribute] = $value;
            }
            $search[$entity] = $data;
        }
        return $search;
    }

    public static function createDefaultSearch($defaultValues)
    {
        $attributes = explode(';', $defaultValues);

        $defaultSearch = array();
        foreach($attributes as $attribute)
        {
            $attribute = explode(',', $attribute);
            $defaultSearch[$attribute[0]] = $attribute[1];
        }
        return $defaultSearch;
    }

}