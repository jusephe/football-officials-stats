<?php

namespace App\Controller;

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

}
