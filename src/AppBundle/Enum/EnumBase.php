<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Apr-16
 * Time: 15:58
 */

namespace AppBundle\Enum;


class EnumBase
{
    public static function matchValueWithGivenEnum($class, $enum, $value)
    {
        if(!class_exists($class)) throw new \Exception('Given class '. $class . ' is not an enum class');

        if(!self::isValidEnum($class, $enum)) throw new \Exception('Specified enum is not a valid enum for class: '.$class.'. Enum specified: '.$enum);

        return strtoupper($enum) == strtoupper($value);
    }

    public static function isValidEnum($class, $value)
    {
        if(!class_exists($class)) return false;

        $match = false;
        foreach(self::getClassConstants($class) as $constantName => $constantValue)
        {
            if(strtoupper($value) == $constantValue)
                $match = true;
        }

        return $match;
    }

    private static function getClassConstants($class)
    {
        $reflect = new \ReflectionClass(get_class(new $class()));
        return $reflect->getConstants();
    }
}