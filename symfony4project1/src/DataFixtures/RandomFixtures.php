<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserAdditionalAttributes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Generator;
use Prophecy\Comparator\Factory;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RandomFixtures extends Fixture {
    private $users;
    /** @var EntityManagerInterface  */
    private $entityManager;

    const USERS = 10;
    const POSTS = 100;
    const CATEGORIES = 10;
    const POSTS_FOR_CATEGORY_MIN = 10;
    const POSTS_FOR_CATEGORY_MAX = 20;
    const USER_INTERESTS_ARRAY = ['baseball', 'football', 'tv', 'computers', 'health', 'science'];

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager) {
        $faker = \Faker\Factory::create();
        $this->resetAutoincrements();
        $this->seedUsers($manager, $faker);
        $this->seedPosts($manager, $faker);
        $this->seedCategories($manager, $faker);
        $this->seedUserAttributes($manager, $faker);
    }

    /**
     * @throws DBALException
     */
    private function resetAutoincrements(){
        $connection = $this->entityManager->getConnection();
        $connection->exec("ALTER TABLE user AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE user_additional_attributes AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE post AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE category AUTO_INCREMENT = 1;");
    }


    private function getRandomUser(ObjectManager $manager): User {
        if (empty($this->users)) {
            $this->users = $manager->getRepository(User::class)->findAll();
        }
        if (empty($this->users)) {
            throw new RuntimeException("Can't find any user in database");
        }
        return $this->users[array_rand($this->users)];
    }

    private function getRandomPost(ObjectManager $manager): Post {
        $randomPostId = mt_rand(1, self::POSTS);
        $post = $manager->getRepository(Post::class)->findOneBy(['id' => $randomPostId]);
        if (empty($post)) {
            throw new RuntimeException("Can't find post with id $randomPostId in database");
        }
        return $post;
    }


    /**
     * @param ObjectManager $manager
     * @param Generator $faker
     * @return void
     */
    private function seedUsers(ObjectManager $manager, Generator $faker): void {
        for ($i = 0; $i <= self::USERS; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setAddress($faker->address);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param Generator $faker
     * @return void
     * @throws Exception
     */
    private function seedPosts(ObjectManager $manager, Generator $faker): void {
        for ($i = 0; $i <= self::POSTS; $i++) {
            $post = new Post();
            $post->setStatus(1);
            $post->setUser($this->getRandomUser($manager));
            $post->setTitle($faker->sentence);
            $post->setContent($faker->realText(500));
            $post->setCreatedAt($faker->dateTimeInInterval('-2 years'));
            $manager->persist($post);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    private function seedCategories(ObjectManager $manager, Generator $faker): void {
        for ($i = 0; $i <= self::CATEGORIES; $i++) {
            $category = new Category();
            $category->setName($faker->sentence(3) . "[" . ($i+1) . "]" );
            $categoriesPerPost = mt_rand(self::POSTS_FOR_CATEGORY_MIN, self::POSTS_FOR_CATEGORY_MAX);
            for ($x = 0; $x <= $categoriesPerPost; $x++) {
                $category->addPost($this->getRandomPost($manager));
            }
            $manager->persist($category);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    private function seedUserAttributes(ObjectManager $manager, Generator $faker): void {
        $allUsers = $manager->getRepository(User::class)->findAll();
        /** @var User $user */
        foreach($allUsers as $user)  {
             $userAttributes = new UserAdditionalAttributes();
             $userAttributes->setAttributesJson($this->getRandomUserAttributesArray($faker));
             $userAttributes->setUser($user);
             $manager->persist($userAttributes);
        }
        $manager->flush();
    }

    private function getRandomUserAttributesArray(Generator $faker) : array {
        $rand = mt_rand(0,3);
        switch ($rand){
            case 0 : return [
                'common_attr' => '123',
                'home_phone' => $faker->phoneNumber,
                'office_phone' => $faker->phoneNumber
            ];
            case 1 : return [
                'common_attr' => '123',
                'wife_name' => $faker->firstNameFemale,
                'age' => $faker->numberBetween(20,50)
            ];
            case 2 : return [
                'common_attr' => '123',
                'interests' => $faker->randomElements(
                                    self::USER_INTERESTS_ARRAY,
                                    mt_rand(1,6)
                               ),
                'age' => $faker->numberBetween(20,50)
            ];
            case 3 : return [
                'common_attr' => '123',
                'home_phone' => $faker->phoneNumber,
                'religion' => $faker->randomElement(['catholic','protestant','mormon'])
            ];
            default: return [];
        }

    }

}
