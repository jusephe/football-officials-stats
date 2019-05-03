<?php


namespace App\Controller;

use App\Entity\Game;
use App\Entity\League;
use App\Entity\Official;
use App\Entity\Team;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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


    /**
     * @Route("/db", name="db")
     */
    public function db(Connection $connection)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $rozh = $this->getDoctrine()
            ->getRepository(Official::class)
            ->find('12345678');

        $tym = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find(1);

        $liga = $this->getDoctrine()
            ->getRepository(League::class)
            ->find(1);

        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find(3);


        $product = new Game();
        $product->setSeason(2018);
        $product->setIsAutumn(true);
        $product->setRound(8);
        $product->setRefereeOfficial($rozh);
        $product->setAwayTeam($tym);
        $product->setHomeTeam($tym);
        $product->setLeague($liga);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId().' game: '.$game->getRound());
    }

}
