<?php

namespace App\Repository;

use App\Entity\RedCard;
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
