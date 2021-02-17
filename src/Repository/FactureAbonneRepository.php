<?php

namespace App\Repository;

use App\Entity\FactureAbonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureAbonne|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureAbonne|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureAbonne[]    findAll()
 * @method FactureAbonne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureAbonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureAbonne::class);
    }

    // /**
    //  * @return FactureAbonne[] Returns an array of FactureAbonne objects
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
    public function findOneBySomeField($value): ?FactureAbonne
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
