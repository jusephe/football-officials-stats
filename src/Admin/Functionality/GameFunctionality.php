<?php

namespace App\Admin\Functionality;

use Doctrine\ORM\EntityManagerInterface;

class GameFunctionality
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDistinctSeasons(): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('g.season')
            ->from('App:Game', 'g')
            ->distinct()
            ->orderBy('g.season', 'DESC')
            ->getQuery();

        $results = $qb->getResult();

        $seasons = array();
        foreach ($results as $res) {
            $seasons[] = $res['season'];
        }

        return $seasons;
    }

    public function getDistinctRounds(): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('g.round')
            ->from('App:Game', 'g')
            ->distinct()
            ->orderBy('g.round', 'ASC')
            ->getQuery();

        $results = $qb->getResult();

        $rounds = array();
        foreach ($results as $res) {
            $rounds[] = $res['round'];
        }

        return $rounds;
    }

}
