<?php

namespace App\Repository;

use App\Entity\LocalesFb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocalesFb|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocalesFb|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocalesFb[]    findAll()
 * @method LocalesFb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalesFbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocalesFb::class);
    }

    // /**
    //  * @return LocalesFb[] Returns an array of LocalesFb objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LocalesFb
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
