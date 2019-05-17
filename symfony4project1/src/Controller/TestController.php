<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\MySerializer;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Util\Debug;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{



    /**
     * @Route("/posts", name="test-posts")
     */
    public function index(MySerializer $mySerializer)
    {
        $man = $this->getDoctrine()->getManager();
        $result = $man
            ->getRepository(Post::class)
            ->findOneBy([],['id' => 'DESC']);
        return new JsonResponse(
            $mySerializer->getSerializer()->serialize($result,'json', [
                'enable_max_depth' => false,
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]));
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
                    ->findOneBy(['id' => 1])
        ]);
}
}
