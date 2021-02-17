<?php

namespace App\Repository;

use App\Entity\Action;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Action|null find($id, $lockMode = null, $lockVersion = null)
 * @method Action|null findOneBy(array $criteria, array $orderBy = null)
 * @method Action[]    findAll()
 * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }

    // /**
    //  * @return Action[] Returns an array of Action objects
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
    public function findOneBySomeField($value): ?Action
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function findTotalReelleAppels(\DateTime $dateLimite)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a.dureeVolumeReelEnHeure')
            ->where(
                    'a.date >= :dateLimit'
            )

            ->setParameters([
                'dateLimit'=>$dateLimite,
            ])
        ;
        return $qb->getQuery()->getResult();
    }

    public function findTop10Data(?\App\Entity\Abonne $abonne, \DateTime $debutTrancheHoraire, \DateTime $finTrancheHoraire)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a.dureeVolumeFactureData')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('a.abonne',':abonne'),
                    'a.heure >= :debutTrancheHoraire',
                    'a.heure <= :finTrancheHoraire'
                )
            )
            ->orderBy('a.dureeVolumeFactureData', 'DESC')
            ->setMaxResults(10)
            ->setParameters([
                'abonne' => $abonne,
                'debutTrancheHoraire' => $debutTrancheHoraire,
                'finTrancheHoraire' => $finTrancheHoraire
            ])
        ;
        return $qb->getQuery()->getResult();
    }


}
