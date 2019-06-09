<?php

namespace App\Admin\Repository;

use App\Admin\Entity\NominationList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NominationList|null find($id, $lockMode = null, $lockVersion = null)
 * @method NominationList|null findOneBy(array $criteria, array $orderBy = null)
 * @method NominationList[]    findAll()
 * @method NominationList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NominationListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NominationList::class);
    }

    // /**
    //  * @return NominationList[] Returns an array of NominationList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NominationList
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
