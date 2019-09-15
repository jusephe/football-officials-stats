<?php

namespace App\Admin\Repository;

use App\Admin\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findAllOrderByAdded()
    {
        return $this->findBy([], ['id' => 'DESC']);
    }

    public function findFilteredOrderByAdded($league, $season, $round)
    {
        if ($league) {
            $qb = $this->getEntityManager()->createQueryBuilder();

            $qb->select('g', 'l')
                ->from('App:Game', 'g')
                ->join('g.league', 'l');

            $qb->andWhere('g.league = :league')
                ->setParameter('league', $league);
        }
        else {
            $qb = $this->createQueryBuilder('g');
        }

        if ($season) {
            $qb->andWhere('g.season = :season')
                ->setParameter('season', $season);
        }

        if ($round) {
            $qb->andWhere('g.round = :round')
                ->setParameter('round', $round);
        }

        $qb->orderBy('g.id', 'DESC');

        return $qb->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
