<?php

namespace App\Repository;

use App\Entity\Pigistes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pigistes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pigistes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pigistes[]    findAll()
 * @method Pigistes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PigistesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pigistes::class);
    }

    // /**
    //  * @return Pigistes[] Returns an array of Pigistes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pigistes
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
