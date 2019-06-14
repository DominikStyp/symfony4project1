<?php
/**
 * Created by PhpStorm.
 * User: aniat
 * Date: 08.06.2019
 * Time: 23:11
 */


// tests/Repository/ProductRepositoryTest.php
namespace App\Tests\Repository;

use App\Entity\Post;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RelationPostToUserTest extends KernelTestCase
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

    public function testPostExists()
    {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->find(1)
        ;
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testEntityWasNotRemoved(){
        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find(1);
        $this->assertInstanceOf(User::class, $user);
        $examplePost = $user->getPosts()[0];
        $examplePostID = $examplePost->getId();
        $this->assertInstanceOf(Post::class, $examplePost);
        // we try to remove post from the inverse side of the relationship (user)
        // which should be impossible
        $user->getPosts()->removeElement($examplePost);
        echo PHP_EOL . "Trying to remove post with id: {$examplePostID}" . PHP_EOL;
        $this->entityManager->flush();
        $this->entityManager->clear(); //clear cache
        // now is it really removed from the INVERSE side (user) ??
        $fromDbExamplePost = $this->entityManager->getRepository(Post::class)->find($examplePostID);
        // is this post really removed ?
        // if we include orphanRemoval=true in User entity next to $posts relation,
        // doctrine will remove this Post entity this way, but if we do not, post WON'T be removed from the database
        $this->assertInstanceOf(Post::class, $fromDbExamplePost);
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
