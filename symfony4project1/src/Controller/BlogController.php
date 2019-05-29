<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
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

    const LIMIT_PER_PAGE = 5;
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        // here we have old method of getting repository, which is used in older versions of Symfony
        // now we can use autowiring instead (injecting instance as an argument to the method/constructor)
        $lastPosts = $this->getDoctrine()->getRepository(Post::class)
            ->findBy([],['id' => 'desc']);
        return $this->render('clean-blog/home.html.twig', [ 'posts' => $lastPosts]);
    }
    /**
     * @Route("/userPosts/{userId}", name="userPosts")
     */
    public function userPosts(int $userId, Request $request, PostRepository $repository)
    {
        $pageNr = $request->query->getInt('page', 1);
        $pagination = $repository->findUserPosts($userId, 'p.id', 'DESC', self::LIMIT_PER_PAGE, $pageNr);
        return $this->render('clean-blog/home.html.twig',
            [
                'pagination' => $pagination
            ]);
    }

    /**
     * @Route("/categoryPosts/{categoryId}", name="categoryPosts")
     */
    public function categoryPosts(int $categoryId, Request $request, CategoryRepository $repository)
    {
        $pageNr = $request->query->getInt('page', 1);
        $pagination = $repository->findPosts($categoryId, 'p.id', 'DESC', self::LIMIT_PER_PAGE, $pageNr);
        //VarDumper::dump($pagination);
        return $this->render('clean-blog/home.html.twig',
            [
                'pagination' => $pagination,
                'categoryId' => $categoryId
            ]);
    }

}
