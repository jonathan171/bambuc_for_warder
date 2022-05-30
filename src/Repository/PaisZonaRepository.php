<?php

namespace App\Repository;

use App\Entity\PaisZona;
use App\Entity\Zonas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method PaisZona|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaisZona|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaisZona[]    findAll()
 * @method PaisZona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaisZonaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaisZona::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PaisZona $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PaisZona $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PaisZona[] Returns an array of PaisZona objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    
    public function findOneByZona(array $opciones =[] ): ?PaisZona
    {
        return $this->createQueryBuilder('p')
            ->innerJoin(Zonas::class,'z', Join::WITH,   'p.zona = z.id')
            ->andWhere('p.pais = :val')
            ->setParameter('val', $opciones['pais'])
            ->andWhere('z.tipo = :val1')
            ->setParameter('val1', $opciones['tipo'])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
