<?php

namespace App\Admin\Controller;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(Connection $connection)
    {
        $users = $connection->fetchAll('SELECT * FROM official');

        $user = json_encode($users[0]);

        return $this->render('test.html.twig', ['user' => $user]);
    }

}
