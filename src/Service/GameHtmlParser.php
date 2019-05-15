<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\League;
use App\Entity\Offence;
use App\Entity\Official;
use App\Entity\RedCard;
use App\Entity\Team;
use App\Entity\YellowCard;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManagerInterface;

class GameHtmlParser
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createGame($sourceCode)
    {
        $em = $this->em;

        $game = new Game();


        $rozh = $em->getRepository(Official::class)
            ->find('12345678');
        $tym = $em->getRepository(Team::class)
            ->find(1);
        $liga = $em->getRepository(League::class)
            ->find(1);
        $off = $em->getRepository(Offence::class)
            ->find(1);

        $yellow = new YellowCard();
        $yellow->setMinute(17);
        $game->addYellowCard($yellow);

        $yellow2 = new YellowCard();
        $yellow2->setMinute(88);
        $game->addYellowCard($yellow2);

        $red = new RedCard();
        $red->setMinute(17);
        $red->setPerson('Tomas Repka');
        $red->setTeam($tym);
        $red->setOffence($off);
        $game->addRedCard($red);

        $red2 = new RedCard();
        $red2->setMinute(19);
        $red2->setPerson('Tomas Repka2');
        $red2->setTeam($tym);
        $red2->setOffence($off);
        $game->addRedCard($red2);

        $game->setSeason(2018);
        $game->setIsAutumn(true);
        $game->setRound(8);
        $game->setRefereeOfficial($rozh);
        $game->setAwayTeam($tym);
        $game->setHomeTeam($tym);
        $game->setLeague($liga);


        $crawler = new Crawler($sourceCode);
        $crawler = $crawler->filter('div.book.zapis-report');



        return $game;
    }

}
