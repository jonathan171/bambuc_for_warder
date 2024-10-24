<?php

namespace App\Repository;

use App\Entity\Empresa;
use App\Entity\FacturaResolucion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacturaResolucion>
 *
 * @method FacturaResolucion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacturaResolucion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacturaResolucion[]    findAll()
 * @method FacturaResolucion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturaResolucionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacturaResolucion::class);
    }

    public function add(FacturaResolucion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FacturaResolucion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FacturaResolucion[] Returns an array of FacturaResolucion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FacturaResolucion
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findActive()
        {
            return $this->createQueryBuilder('fr')
                ->innerJoin(Empresa::class, 'e', Join::WITH,   'fr.empresa = e.id')   
                ->andWhere('fr.activo = :val')
                ->andWhere('fr.id != 10')
                ->setParameter('val', 1)
                ->orderBy('e.id', 'ASC')
                ->getQuery()
                ->execute()
            ;
        }

        public function findAlternate()
        {
            return $this->createQueryBuilder('fr')
                ->innerJoin(Empresa::class, 'e', Join::WITH,   'fr.empresa = e.id')   
                ->andWhere('fr.activo = :val')
                ->andWhere('fr.id = 10 OR fr.id = 11')
                ->setParameter('val', 1)
                ->orderBy('e.id', 'ASC')
                ->getQuery()
                ->execute()
            ;
        }
}
