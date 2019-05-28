<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\MySerializer;
use Doctrine\DBAL\Tools\Dumper;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Util\Debug;
use Symfony\Component\VarDumper\VarDumper;


class BlogController extends AbstractController
{

    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        $lastPosts = $this->getDoctrine()->getRepository(Post::class)
            ->findBy([],['id' => 'desc']);
        return $this->render('clean-blog/home.html.twig', [ 'posts' => $lastPosts]);
    }
    /**
     * @Route("/userPosts/{id}", name="userPosts")
     */
    public function userPosts(int $id)
    {
        $lastPosts = $this->getDoctrine()->getRepository(Post::class)
            ->findBy(['user_id' => $id],['id' => 'desc']);
        return $this->render('clean-blog/home.html.twig', [ 'posts' => $lastPosts]);
    }

    /**
     * @Route("/categoryPosts/{categoryId}", name="categoryPosts")
     */
    public function categoryPosts(int $categoryId)
    {
        /** @var Category $category */
        $lastPosts = $this->getDoctrine()->getRepository(Category::class)
            ->findPosts($categoryId, 'c.id', 'DESC', 10);
        return $this->render('clean-blog/home.html.twig', [ 'posts' => $lastPosts, 'categoryId' => $categoryId ]);
    }

}
