<?php

namespace App\Repository;

use App\Entity\EnviosNacionalesUnidades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnviosNacionalesUnidades>
 *
 * @method EnviosNacionalesUnidades|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnviosNacionalesUnidades|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnviosNacionalesUnidades[]    findAll()
 * @method EnviosNacionalesUnidades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnviosNacionalesUnidadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnviosNacionalesUnidades::class);
    }

    public function add(EnviosNacionalesUnidades $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnviosNacionalesUnidades $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return EnviosNacionalesUnidades[] Returns an array of EnviosNacionalesUnidades objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EnviosNacionalesUnidades
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
