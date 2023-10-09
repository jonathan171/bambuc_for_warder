<?php

namespace App\Repository;

use App\Entity\TrazabilidadEnvioNacional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrazabilidadEnvioNacional>
 *
 * @method TrazabilidadEnvioNacional|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrazabilidadEnvioNacional|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrazabilidadEnvioNacional[]    findAll()
 * @method TrazabilidadEnvioNacional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrazabilidadEnvioNacionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrazabilidadEnvioNacional::class);
    }

    public function add(TrazabilidadEnvioNacional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TrazabilidadEnvioNacional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TrazabilidadEnvioNacional[] Returns an array of TrazabilidadEnvioNacional objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TrazabilidadEnvioNacional
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
