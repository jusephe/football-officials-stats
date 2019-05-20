<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\League;
use App\Entity\Offence;
use App\Entity\Official;
use App\Entity\RedCard;
use App\Entity\Team;
use App\Entity\YellowCard;
use App\Repository\LeagueRepository;
use Symfony\Component\DomCrawler\Crawler;
use Doctrine\ORM\EntityManagerInterface;

class GameHtmlParser
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)  // staci, jednotlive repository volat pres EM
    {
        $this->entityManager = $entityManager;
    }

    public function createGame($sourceCode)
    {
        $em = $this->entityManager;



        // sem vlozit kod z controlleru



        //return $game;
    }

}
