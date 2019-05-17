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



        // sem vlozit kod z controlleru


        /*  na smazani
        $yellow = new YellowCard();
        $yellow->setMinute(17);
        $game->addYellowCard($yellow);

        $red = new RedCard();
        $red->setMinute(17);
        $red->setPerson('Tomas Repka');
        $red->setTeam($tym);
        $red->setOffence($off);
        $game->addRedCard($red);

        $game->setRefereeOfficial($rozh);
        $game->setAwayTeam($tym);
        $game->setHomeTeam($tym);
        */


        //return $game;
    }

}
