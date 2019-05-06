<?php

namespace App\Repository;

use App\Entity\Offence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Offence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offence[]    findAll()
 * @method Offence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Offence::class);
    }

    // /**
    //  * @return Offence[] Returns an array of Offence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Offence
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
