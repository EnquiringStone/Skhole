<?php

namespace AppBundle;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Doctrine\ORM\EntityManager;

class AppBundle extends Bundle
{
    public function boot()
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        Type::addType('coursecontentenum', 'AppBundle\Enum\CreatedCourseContentEnum');
        $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('coursecontentenum', 'coursecontentenum');
    }
}
