<?php

namespace App\Admin\Repository;

use App\Admin\Entity\Official;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Official|null find($id, $lockMode = null, $lockVersion = null)
 * @method Official|null findOneBy(array $criteria, array $orderBy = null)
 * @method Official[]    findAll()
 * @method Official[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficialRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Official::class);
    }

    public function findAllOrderByName()
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function findWithoutNominationList($year, $part)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT IDENTITY (nl.official)
                  FROM App\Admin\Entity\NominationList nl
                  WHERE (nl.year = :year AND nl.partOfSeason = :part)'
        );
        $query->setParameter('year', $year)
            ->setParameter('part', $part);

        $officialsWithEntry = $query->execute();


        $queryBuilder = $this->createQueryBuilder('o');
        if ($officialsWithEntry) {
            $queryBuilder->andWhere('o.id NOT IN (:officialsWithEntry)')
                ->setParameter('officialsWithEntry', $officialsWithEntry);
        }
        $queryBuilder->orderBy('o.name', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    // /**
    //  * @return Official[] Returns an array of Official objects
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
    public function findOneBySomeField($value): ?Official
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
