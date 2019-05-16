<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture {
    private $users;

    const USERS = 10;
    const POSTS = 200;
    const CATEGORIES = 20;
    const POSTS_FOR_CATEGORY_MIN = 10;
    const POSTS_FOR_CATEGORY_MAX = 30;

    public function load(ObjectManager $manager) {
        $faker = \Faker\Factory::create();
        $this->seedUsers($manager, $faker);
        $this->seedPosts($manager, $faker);
        $this->seedCategories($manager);
    }

    private function getRandomUser(ObjectManager $manager): User {
        if (empty($this->users)) {
            $this->users = $manager->getRepository(User::class)->findAll();
        }
        if (empty($this->users)) {
            throw new \Exception("Can't find any user in database");
        }
        return $this->users[array_rand($this->users)];
    }

    private function getRandomPost(ObjectManager $manager): Post {
        $randomPostId = mt_rand(1, self::POSTS);
        $post = $manager->getRepository(Post::class)->findOneBy(['id' => $randomPostId]);
        if (empty($post)) {
            throw new \Exception("Can't find post with id $randomPostId in database");
        }
        return $post;
    }


    /**
     * @param ObjectManager $manager
     * @param \Faker\Generator $faker
     * @return void
     */
    private function seedUsers(ObjectManager $manager, \Faker\Generator $faker): void {
        for ($i = 0; $i <= self::USERS; $i++) {
            $user = new User();
            $user->setName($faker->name);
            $user->setAddress($faker->address);
            $user->setEmail($faker->email);
            $manager->persist($user);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param \Faker\Generator $faker
     * @return void
     * @throws \Exception
     */
    private function seedPosts(ObjectManager $manager, \Faker\Generator $faker): void {
        for ($i = 0; $i <= self::POSTS; $i++) {
            $post = new Post();
            $post->setStatus(1);
            $post->setUser($this->getRandomUser($manager));
            $post->setTitle($faker->sentence);
            $post->setContent($faker->text);
            $manager->persist($post);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    private function seedCategories(ObjectManager $manager): void {
        for ($i = 0; $i <= self::CATEGORIES; $i++) {
            $category = new Category();
            $category->setName("Category " . $i);
            $categoriesPerPost = mt_rand(self::POSTS_FOR_CATEGORY_MIN, self::POSTS_FOR_CATEGORY_MAX);
            for ($x = 0; $x <= $categoriesPerPost; $x++) {
                $category->addPost($this->getRandomPost($manager));
            }
            $manager->persist($category);
        }
        $manager->flush();
    }


}
