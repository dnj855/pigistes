<?php

namespace App\Repository;

use App\Entity\FbLocale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FbLocale|null find($id, $lockMode = null, $lockVersion = null)
 * @method FbLocale|null findOneBy(array $criteria, array $orderBy = null)
 * @method FbLocale[]    findAll()
 * @method FbLocale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FbLocaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FbLocale::class);
    }

    // /**
    //  * @return FbLocale[] Returns an array of FbLocale objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FbLocale
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
