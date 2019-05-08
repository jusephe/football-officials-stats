<?php

namespace App\Controller;

use Demontpx\ParsedownBundle\Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @Route("/admin/add-game", name="add_game")
     */
    public function addGame()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/punishments", name="punishments")
     */
    public function punishments()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/nomination-list", name="nomination_list")
     */
    public function nominationList()
    {

        return $this->render('admin/base.html.twig');
    }

    /**
     * @Route("/admin/news", name="news")
     */
    public function news(Parsedown $parsedown)
    {
        $text = $parsedown->text('Hello _Parsedown_!'); # prints: <p>Hello <em>Parsedown</em>!</p>

        return $this->render('test.html.twig', ['text' => $text]);
    }

}
