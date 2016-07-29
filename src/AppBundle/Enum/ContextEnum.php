<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 28-Apr-16
 * Time: 15:49
 */

namespace AppBundle\Enum;


class ContextEnum extends EnumBase
{
    /**
     * Used for everything that the public can see
     */
    const PUBLIC_CONTEXT = 'PUBLIC';

    /**
     * Used for everything that the logged in user can see (edit, create, delete, etc.)
     */
    const SELF_CONTEXT = 'SELF';

    /**
     * Used for search related business
     */
    const SEARCH_CONTEXT = 'SEARCH';

    /**
     * Used for getting the session id
     */
    const ANONYMOUS_CONTEXT = 'ANONYMOUS';
}