<?php

namespace App\Repository;

use App\Entity\Tarifas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @method Tarifas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tarifas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tarifas[]    findAll()
 * @method Tarifas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TarifasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tarifas::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tarifas $entity, bool $flush = true): void
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
    public function remove(Tarifas $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Tarifas[] Returns an array of Tarifas objects
    //  */
    
    public function findByDataTable(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('t')
        ->innerJoin('t.zona','z');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('t.pesoMinimo like :val OR t.pesoMaximo like :val2 OR t.costoFlete like :val3 OR z.nombre like :val4')
            ->setParameters(['val'=>$shearch,'val2'=>$shearch,'val3'=>$shearch,'val4'=>$shearch]);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {
           
            $list[] = ['pesoMinimo'=>$item->getPesoMinimo(),'pesoMaximo'=>$item->getPesoMaximo(),'costoFlete'=>$item->getCostoFlete(),'zona'=>$item->getZona()->getNombre(),'total'=>$item->getTotal()];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }
    

    /*
    public function findOneBySomeField($value): ?Tarifas
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
