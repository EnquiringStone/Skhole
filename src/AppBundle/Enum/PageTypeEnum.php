<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 07-May-16
 * Time: 16:33
 */

namespace AppBundle\Enum;

class PageTypeEnum extends EnumBase
{
    /**
     * The standard pages of a course. Such as the planning, schedule, course card, etc.
     */
    const STANDARD_TYPE = "STANDARD";

    /**
     * The custom pages of a course. Such as the open questions and explanations
     */
    const CUSTOM_TYPE = "CUSTOM";

    /**
     * The pages used for the publish process. Such as the last page of the create course process
     */
    const PUBLISH_TYPE = "PUBLISH";
}