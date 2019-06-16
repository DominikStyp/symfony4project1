<?php
/**
 * Created by PhpStorm.
 * User: aniat
 * Date: 15.06.2019
 * Time: 00:57
 */

namespace App\Tests\Repository;


use App\Entity\User;
use App\EventListeners\EventListenerWhichChangesEmailOnUpdate;
use App\Tests\CommonTestCase;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Events;

class DoctrineEventsListenersTest extends CommonTestCase
{
    public function testPreUserUpdate()
    {
        $eventManager = $this->entityManager->getEventManager();
        // hooking event listener to the event manager wchis is connected to the entity manager (ufff...)
        $eventManager->addEventListener([Events::preUpdate], new EventListenerWhichChangesEmailOnUpdate());
        /** @var User $user */
        $user = $this->entityManager->find(User::class, 1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEquals(EventListenerWhichChangesEmailOnUpdate::TEST_EMAIL, $user->getEmail());
        // now we try to change user email, but we should fail to do it
        $user->setEmail("tryHard@gmail.com");
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // well ??? is it changed ? it shouldn't be because we hacked it right ?
        $this->assertNotEquals("tryHard@gmail.com", $user->getEmail());
        // but if we changed it to hacked one ?
        $this->assertEquals(EventListenerWhichChangesEmailOnUpdate::TEST_EMAIL, $user->getEmail());

    }
}