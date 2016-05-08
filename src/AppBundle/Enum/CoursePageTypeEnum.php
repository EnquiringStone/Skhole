<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 08-May-16
 * Time: 16:48
 */

namespace AppBundle\Enum;

/**
 * These are the possible values of a course page type as defined in the database
 *
 * @package AppBundle\Enum
 */
class CoursePageTypeEnum extends EnumBase
{
    /**
     * Indicates a page is an exercise and has question associated
     */
    const ExerciseType = "EXERCISE";

    /**
     * Indicates a page only consists out of text
     */
    const TextType = "TEXT";

    /**
     * Indicates a page has both text and/or a video
     */
    const VideoTextType = "VIDEO-TEXT";
}