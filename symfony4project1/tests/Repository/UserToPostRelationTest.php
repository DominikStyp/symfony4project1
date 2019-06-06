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
    public function testChangeInverseAndOwningSideOfUserToPostRelation()
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find(1);
        /** @var Post $post */
        $post = $user->getPosts()->current();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Post::class, $post);
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