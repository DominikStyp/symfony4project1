<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-06-06
 * Time: 01:29
 */

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserToPostRelationTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * User (inverse side) $posts -> OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
     * Post (owning side)   $user -> ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     *  !!! if we change User (inverse side) from Post perspective chagnes WON'T BE CONSIDERED!
     *  !!! if we change Post (owning side) from User perspective changes WILL BE CONSIDERED!
     */
    public function testInverseSideOfUserToPostRelation()
    {
        /** @var User $user */
        $userId = 1;
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        /** @var Post $postNr2ForSwap */
        // find post of other user to swap with user 1
        $postNr2ForSwap = $this->entityManager->getRepository(Post::class)
            ->findOneBy(['user_id' => 2], ['id' => 'ASC']);
        ///........ trying to swap 2 posts, and persist changes
        $user->getPosts()[0] = $postNr2ForSwap;
        $this->entityManager->flush();

        // checking if post was swapped so it has the same title
        // in user object is seems that titles match
        $this->assertEquals(
            $user->getPosts()[0]->getTitle(),
            $postNr2ForSwap->getTitle()
        );
        // WARNING HERE!!! We must clear() the Entity Manager, otherwise it does not get us FRESH record from the database,
        // and it will retain wrong (not persisted) record in memory
        $this->entityManager->clear();
        /** @var Post $newPost */
        $newPost = $this->entityManager->getRepository(User::class)
            ->find($userId)
            ->getPosts()[0];
        // If we check the same post fresh from the database ($newPost)
        // it seems that it hasn't changed at all
        $this->assertNotEquals(
            $newPost->getTitle(),
            $postNr2ForSwap->getTitle()
        );

    }


    public function testOwningSideOfUserToPostRelation()
    {
        /** @var Post $postNr1 */
        $postNr1 = $this->entityManager->getRepository(Post::class)->findOneBy(['user_id' => 1], ['id' => 'ASC']);
        $postNr2 = $this->entityManager->getRepository(Post::class)->findOneBy(['user_id' => 2], ['id' => 'ASC']);
        $postId1 = $postNr1->getId();
        $postId2 = $postNr2->getId();
        echo PHP_EOL."Changing post with id: $postId1 to user_id=2".PHP_EOL;
        // check if users from post1 and post2 ARE NOT THE SAME
        $this->assertNotEquals($postNr1->getUser()->getId(), $postNr2->getUser()->getId());
        $user2 = $postNr2->getUser();
        // swapping users
        $postNr1->setUser($user2);
        // and now changes are persisted to database and user is changed for post nr 1
        $this->entityManager->flush();
        $this->entityManager->clear();
        $postNr1 = $this->entityManager->getRepository(Post::class)->find($postId1);
        $postNr2 = $this->entityManager->getRepository(Post::class)->find($postId2);
        // THIS TIME THOSE 2 POSTS SHOULD HAVE THE SAME USER!
        $this->assertEquals($postNr1->getUser()->getId(), $postNr2->getUser()->getId());

    }



    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}