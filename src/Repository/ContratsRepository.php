<?php

namespace App\Repository;

use App\Entity\Contrats;
use App\Entity\Pigistes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contrats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contrats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contrats[]    findAll()
 * @method Contrats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrats::class);
    }

    public function getContractAmount(\DateTimeImmutable $date)
    {
        $date_debut = new \DateTime();
        $date_debut->setDate($date->format('Y'), 1, 1);
        $date_debut->setTime(0, 0, 0, 0);
        $date_fin = new \DateTime();
        $date_fin->setDate($date->format('Y'), 12, 31);
        $date_fin->setTime(23, 59, 59, 59);
        return $this->createQueryBuilder('c')
            ->where('c.date_debut >= :date_debut')
            ->andWhere('c.date_fin <= :date_fin')
            ->andWhere('c.active = true')
            ->setParameter('date_debut', $date_debut)
            ->setParameter('date_fin', $date_fin)
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findFirstContract()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.date_debut', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastContract()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.numero', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getContractByYear($year)
    {
        $startDate = new \DateTime("$year-01-01T00:00:00");
        $endDate = (clone $startDate)->modify('last day of this month');
        return $this->createQueryBuilder('c')
            ->where('c.date_debut >= :startDate')
            ->andWhere('c.date_fin <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getContractByMonth($year, $month)
    {
        $startDate = new \DateTime("$year-$month-01T00:00:00");
        $endDate = (clone $startDate)->modify('last day of this month');
        return $this->createQueryBuilder('c')
            ->where('c.date_debut >= :startDate')
            ->andWhere('c.date_fin <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('c.date_debut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDrpContract(Pigistes $pigistes, $year = null, $month = null)
    {
        if ($month === null) {
            $month = (int)date('m');
        }
        if ($year === null) {
            $year = (int)date('Y');
        }
        $startDate = new \DateTime("$year-$month-01T00:00:00");
        $endDate = (clone $startDate)->modify('last day of this month');
        return $this->createQueryBuilder('c')
            ->where('c.date_debut >= :startDate')
            ->andWhere('c.date_fin <= :endDate')
            ->andWhere('c.pigiste = :pigiste')
            ->andWhere('c.active = true')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('pigiste', $pigistes)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Contrats[] Returns an array of Contrats objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contrats
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
