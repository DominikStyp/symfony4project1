<?php


namespace App\Tests\DoctrineFeatures;


use App\Entity\User;
use App\Tests\CommonTestCase;

class DoctrineCacheTest extends CommonTestCase
{
    private function retrieveCachedEmailForUser(int $id, int $cacheLifetime = 15)
    {
        $query = $this
            ->entityManager
            ->createQuery('select u from '.User::class.' u where u.id = :userId')
            ->setParameter('userId', $id);
        $query->useResultCache(true);
        $query->setLifetime($cacheLifetime);
        $result = $query->execute();
        $email = $result[0]->getEmail();
        return $email;
    }


    public function testDoctrineCacheResult()
    {
        // first part cached query
        $email =  $this->retrieveCachedEmailForUser(1, 15);
        // lets validate if this is an email
        $this->assertTrue(strpos($email,'@') !==false );

        // second part remove the user (non cached)
        $user = $this->entityManager->find(User::class, 1);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // third part retrieve user again using same method which is still cached, but does not exist in database anymore
        $email2 =  $this->retrieveCachedEmailForUser(1, 15);
        $this->assertEquals($email, $email2);

        // part four check if user really does not exist in DB anymore, it should not...
        $this->entityManager->clear();
        $user = $this->entityManager->find(User::class, 1);
        $this->assertEmpty($user);
    }
}