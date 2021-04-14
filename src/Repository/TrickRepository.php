<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @param Group $group
     * @return Trick[] Returns an array Trick objects
     */
    public function group(Group $group): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.group = :group_id')
            ->setParameter('group_id', $group->getId())
            ->orderBy('t.created_at')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param int $page
     * @param int $items_per_page
     *
     * @return array
     */
    public function paginate(int $page = 0, int $items_per_page = 10): array
    {

        $starter = ($page * $items_per_page);

        return $this->createQueryBuilder('t')
            ->orderBy('t.created_at', 'desc')
            ->setFirstResult($starter)
            ->setMaxResults($items_per_page)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    // /**
    //  * @return Trick[] Returns an array of Trick objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('t')
    ->andWhere('t.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('t.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?Trick
{
return $this->createQueryBuilder('t')
->andWhere('t.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}
