<?php

namespace App\Repository;

use App\Entity\YellowCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method YellowCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method YellowCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method YellowCard[]    findAll()
 * @method YellowCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YellowCardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, YellowCard::class);
    }

    // /**
    //  * @return YellowCard[] Returns an array of YellowCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?YellowCard
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
