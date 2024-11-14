<?php

namespace App\Repository;

use App\Entity\ReciboCajaItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReciboCajaItem>
 *
 * @method ReciboCajaItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReciboCajaItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReciboCajaItem[]    findAll()
 * @method ReciboCajaItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReciboCajaItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReciboCajaItem::class);
    }

    public function add(ReciboCajaItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReciboCajaItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReciboCajaItem[] Returns an array of ReciboCajaItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReciboCajaItem
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
