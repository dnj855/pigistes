<?php

namespace App\Repository;

use App\Entity\SiteParameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SiteParameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method SiteParameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method SiteParameters[]    findAll()
 * @method SiteParameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteParameters::class);
    }

    public function findLastTarifsUpdate()
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.tarifsUpdate IS NOT NULL')
            ->orderBy('t.tarifsUpdate','DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getResult();
    }

    // /**
    //  * @return SiteParameters[] Returns an array of SiteParameters objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SiteParameters
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
