<?php


namespace App\Tests\DoctrineFeatures;


use App\Entity\User;
use App\Tests\CommonTestCase;
use Doctrine\ORM\Query\ResultSetMapping;

class DoctrineCacheTest extends CommonTestCase
{


    /**
     * @param int $id
     * @param int $cacheLifetime
     * @return User
     */
    private function retrieveCachedUser(int $id, int $cacheLifetime = 15){
        $query = $this->getUserCachedQuery($id, $cacheLifetime);
        $result = $query->execute();
        return $result[0];
    }

    /**
     * @param int $id
     * @param int $cacheLifetime
     * @return \Doctrine\ORM\Query
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    private function getUserCachedQuery(int $id, int $cacheLifetime) {
        /**
         *  !!!
         *  REMEMBER: If you wan't to retrieve User from database
         *  you MUST invoke clear(User::class) to detach User entity from the manager,
         *  so it can retrieve a new version of entiny from the database, especially if you used RAW query somewhere else
         *  !!!
         */
        $this->entityManager->clear(User::class);
        $query = $this->entityManager
            ->createQuery('select u from ' . User::class . ' u where u.id = :userId')
            ->setParameter('userId', $id);
        $query->useResultCache(true, $cacheLifetime, "retrieve_user_$id");
        return $query;
    }


    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function testDoctrineCacheResult()
    {
        // first part cached query
        $email =  $this->retrieveCachedUser(1, 15)->getEmail();
        // lets validate if this is an email
        $this->assertTrue(strpos($email,'@') !==false );

        // second part remove the user (non cached)
        $user = $this->entityManager->find(User::class, 1);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // third part retrieve user again using same method which is still cached, but does not exist in database anymore
        $email2 =  $this->retrieveCachedUser(1, 15)->getEmail();
        $this->assertEquals($email, $email2);

        // part four check if user really does not exist in DB anymore, it should not...
        $user = $this->entityManager->find(User::class, 1);
        $this->assertEmpty($user);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testDoctrineCacheResultWithWaitForExpiration()
    {
        // retrieve cached user
        $query =  $this->getUserCachedQuery(2, 5);
        /** @var User $user */
        $user = $query->execute()[0];
        $this->assertEquals('john.doe2@gmail.com', $user->getEmail());

        // change email in
        $stmt = $this->entityManager->getConnection()->prepare(" UPDATE user SET email = :email WHERE id = :userId ");
        $stmt->execute([':email' => 'something@gmail.com', ':userId' => 2]);

        // again execute the same query (4 seconds not passed yet...)
        $user1 = $query->execute()[0];
        $this->assertEquals('john.doe2@gmail.com', $user1->getEmail());
        $this->entityManager->clear(User::class);

        // after 1 second cache is not expired so it still should be john.doe2
        // REMEMBER EACH TIME WANT TO GET
        sleep(1);
        $this->entityManager->clear(User::class);
        $user2 = $query->execute()[0];
        $this->assertEquals('john.doe2@gmail.com', $user2->getEmail());

        // sleep for 5 additional seconds cache SHOULD expire, and we should get NEW result 'something@gmail.com' from database
        sleep(5);
        $this->entityManager->clear(User::class);
        $user3 = $query->execute()[0];
        $this->assertEquals('something@gmail.com', $user3->getEmail());

    }


}