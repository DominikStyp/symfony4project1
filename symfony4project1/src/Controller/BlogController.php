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
class BlogController extends AbstractController
{

    /**
     * @Route("/clean-blog", name="clean-blog")
     */
    public function clean_blog()
    {
        return $this->render('clean-blog/index.html.twig');
    }


}
