<?php

namespace App\Admin\Repository;

use App\Admin\Entity\RedCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RedCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method RedCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method RedCard[]    findAll()
 * @method RedCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RedCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RedCard::class);
    }

    public function findByLeagueSeasonPart($league, $season, $part)
    {
        $leaguesQB = $this->getEntityManager()->createQueryBuilder();
        $redcardsQB = $this->createQueryBuilder('rc');

        $leaguesQB->select('l')
            ->from('App\Admin\Entity\League', 'l')
            ->where($leaguesQB->expr()->eq('l.shortName',':league'));

        $redcardsQB->join('rc.game', 'g')
            ->where($redcardsQB->expr()->andX(
                $redcardsQB->expr()->eq('g.season', ':season'),
                $redcardsQB->expr()->in('g.league',  $leaguesQB->getDQL())
            ))
            ->orderBy('g.round')
            ->setParameter('league', $league)
            ->setParameter('season', $season);

        if($part !== null) {
            $isAutumn = 1;
            if ($part === 'jaro') $isAutumn = 0;

            $redcardsQB->andWhere($redcardsQB->expr()->eq('g.isAutumn', ':isAutumn'))
                ->setParameter('isAutumn', $isAutumn);
        }

        return $redcardsQB->getQuery()->getResult();
    }

    // /**
    //  * @return RedCard[] Returns an array of RedCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RedCard
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
