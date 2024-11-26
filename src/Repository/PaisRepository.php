<?php

namespace App\Repository;

use App\Entity\Pais;
use App\Entity\Zonas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pais|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pais|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pais[]    findAll()
 * @method Pais[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pais::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Pais $entity, bool $flush = true): void
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
    public function remove(Pais $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByDataTable(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('p');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('p.nombre like :val')
            ->setParameters(['val'=>$shearch]);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/pais/'.$item->getId().'/edit">editar</a>';
           
            $list[] = ['nombre'=>$item->getNombre(),
                       'actions'=> $actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

    public function findByDataShearch(array $options = [])
    {
        
        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('c');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->where('c.nombre like :val ')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];

          $list[] = [
            'id' => "",       
            'text'=>"Sin especificar"
        ];
        foreach ($paginator as $item) {

            $list[] = [
                'id' => $item->getId(),       
                'text'=>$item->getNombre()
            ];
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

    /*
    public function findOneBySomeField($value): ?Pais
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
