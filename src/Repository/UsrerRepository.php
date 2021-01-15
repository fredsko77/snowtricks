<?php

namespace App\Repository;

use App\Entity\Usrer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Usrer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usrer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usrer[]    findAll()
 * @method Usrer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsrerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usrer::class);
    }

    // /**
    //  * @return Usrer[] Returns an array of Usrer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Usrer
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
