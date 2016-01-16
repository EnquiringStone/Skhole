<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 16-Jan-16
 * Time: 01:03
 */

namespace AppBundle\Enum;


class CreatedCourseContentEnum extends EnumType
{
    protected $name = "coursecontentenum";
    protected $values = array("CourseOpenQuestions", "CourseMultipleChoices");
}