<?php


namespace App\DataFixtures;


use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserAdditionalAttributes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory;
use Faker\Generator;

class StaticFixtures extends Fixture {
    private $users;
    /** @var EntityManagerInterface  */
    private $entityManager;

    const USERS = 20;
    const POSTS = 100;
    const CATEGORIES = 10;
    const POSTS_FOR_CATEGORY = 10;
    const USER_INTERESTS_ARRAY = ['baseball', 'football', 'tv', 'computers', 'health', 'science'];

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager) {
        $faker = Factory::create();
        $this->resetAutoincrements();
        $this->seedUsers($manager, $faker);
        $this->seedPosts($manager, $faker);
        $this->seedCategories($manager, $faker);
        $this->seedUserAttributes($manager, $faker);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function resetAutoincrements(){
        $connection = $this->entityManager->getConnection();
        $connection->exec("ALTER TABLE user AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE user_additional_attributes AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE post AUTO_INCREMENT = 1;");
        $connection->exec("ALTER TABLE category AUTO_INCREMENT = 1;");
    }



    /**
     * @param ObjectManager $manager
     * @param Generator $faker
     * @return void
     */
    private function seedUsers(ObjectManager $manager, Generator $faker): void {
        for ($i = 1; $i <= self::USERS; $i++) {
            $user = new User();
            $user->setName("John Doe $i");
            $user->setAddress("Poland, Krakow ul. Balicka $i");
            $user->setEmail("john.doe{$i}@gmail.com");
            $user->setPassword("123321");
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
        for ($i = 1; $i <= self::POSTS; $i++) {
            $userId = ceil($i/10);
            $userObj = $manager->find(User::class, $userId);
            if(!$userObj instanceof User){
                throw new \Exception(("userObj is not instance of Entity User, for user_id: $userId. Try to increase the number of users added to database to avoid this error."));
            }
            $post = new Post();
            $post->setStatus(1);
            $post->setUser($userObj);
            $post->setTitle("Post nr $i");
            $post->setContent("This is some post. This post should be static. Only thing that differs me from another post is my unique number. My UNIQUE_ID:{$i}");
            $post->setCreatedAt($faker->dateTimeInInterval('-2 years'));
            $manager->persist($post);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    private function seedCategories(ObjectManager $manager, Generator $faker): void {
        $postCounter = 1;
        for ($i = 1; $i <= self::CATEGORIES; $i++) {
            $category = new Category();
            $category->setName("Category nr:$i" );
            for ($x = 1; $x <= self::POSTS_FOR_CATEGORY; $x++) {
                $postId = $postCounter;
                $postObj = $manager->find(Post::class, $postId);
                //echo PHP_EOL."post_id:{$postId}, category_id:{$i}";
                if(!$postObj instanceof Post){
                    throw new \Exception(("userObj is not instance of Entity Post, for post_id: $postId. Try to increase the number of posts added to database to avoid this error."));
                }
                $category->addPost($postObj);
                $postCounter++;
            }
            $manager->persist($category);
        }
       // echo PHP_EOL;
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
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