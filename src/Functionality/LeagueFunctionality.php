<?php

namespace App\Functionality;

use Doctrine\ORM\EntityManagerInterface;

class LeagueFunctionality
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDistinctShortNames( ): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('l.shortName')
            ->from('App:League', 'l')
            ->distinct()
            ->getQuery();

        $results = $qb->getResult();

        $shortNames = array();
        foreach($results as $res){
            $shortNames[] = $res['shortName'];
        }

        return $shortNames;
    }

}
