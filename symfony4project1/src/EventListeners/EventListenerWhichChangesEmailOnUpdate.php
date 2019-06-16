<?php
/**
 * Created by PhpStorm.
 * User: aniat
 * Date: 15.06.2019
 * Time: 01:14
 */

namespace App\EventListeners;


use App\Entity\User;
use \Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class EventListenerWhichChangesEmailOnUpdate
{
    const TEST_EMAIL = "changed@gmail.com";

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof User) {
            $entity->setEmail(self::TEST_EMAIL);
        }
    }
}