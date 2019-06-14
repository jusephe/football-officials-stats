<?php

namespace App\Site\Controller;

use App\Admin\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAllOrderByPublished();

        return $this->render('site/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/novinky/{id}", name="post", requirements={"id"="\d+"})
     */
    public function post($id, PostRepository $postRepository)
    {
        $post = $postRepository->find($id);
        if ($post === null) throw $this->createNotFoundException('Taková novinka neexistuje!');

        return $this->render('site/post.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/o-projektu", name="about")
     */
    public function about()
    {
        return $this->render('site/base.html.twig');
    }

    /**
     * @Route("/prebor", name="prebor")
     */
    public function prebor()
    {
        return $this->render('site/base.html.twig');
    }

    /**
     * @Route("/a-trida", name="a_trida")
     */
    public function aTrida()
    {
        return $this->render('site/base.html.twig');
    }

    /**
     * @Route("/rozhodci", name="site_officials")
     */
    public function officials()
    {
        return $this->render('site/base.html.twig');
    }

    /**
     * @Route("/delegati", name="site_assessors")
     */
    public function assessors()
    {
        return $this->render('site/base.html.twig');
    }

}
