<?php

namespace App\Repository;

use App\Entity\Empresa;
use App\Entity\Municipio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Municipio>
 *
 * @method Municipio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Municipio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Municipio[]    findAll()
 * @method Municipio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MunicipioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Municipio::class);
    }

    public function add(Municipio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Municipio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Municipio[] Returns an array of Municipio objects
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

//    public function findOneBySomeField($value): ?Municipio
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

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/municipio/'.$item->getId().'/edit">editar</a>';
           
            $list[] = ['nombre'=>$item->getNombre(),
                       'actions'=> $actions];
           // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }
}
