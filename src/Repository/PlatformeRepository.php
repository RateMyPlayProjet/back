<?php

namespace App\Repository;

use App\Entity\Platforme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Platforme>
 *
 * @method Platforme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platforme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platforme[]    findAll()
 * @method Platforme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Platforme::class);
    }

//    /**
//     * @return Platforme[] Returns an array of Platforme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Platforme
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
