<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\MySerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{



    /**
     * @Route("/posts", name="posts")
     */
    public function index(MySerializer $mySerializer)
    {
        $man = $this->getDoctrine()->getManager();
        $result = $man
            ->getRepository(Post::class)
            ->findOneBy([],['id' => 'DESC']);
        return $this->json([
            'post' => $mySerializer->getSerializer()->serialize($result,'json')

        ]);
    }

    /**
     * @Route("/insert-test-new-post", name="insert-test-new-post")
     */
    public function insertTestNewPost(){
        $man = $this->getDoctrine()->getManager();
        $post = new Post();
        $post->setContent("Test post blah blah blah");
        $post->setTitle("TITLE FOR TEST POST");
        $post->setUserId(1);
        $post->setStatus(1);
        $man->persist($post);
        $man->flush();
        return $this->json([
            'post' =>
                $man
                    ->getRepository(Post::class)
                    ->findOneBy(['id' => 2])
        ]);
}
}
