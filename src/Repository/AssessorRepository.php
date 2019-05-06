<?php

namespace App\Repository;

use App\Entity\Assessor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Assessor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assessor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assessor[]    findAll()
 * @method Assessor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssessorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Assessor::class);
    }

    // /**
    //  * @return Assessor[] Returns an array of Assessor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Assessor
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
