<?php

namespace App\Repository;

use App\Entity\Clientes;
use App\Entity\Municipio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clientes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clientes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clientes[]    findAll()
 * @method Clientes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clientes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Clientes $entity, bool $flush = true): void
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
    public function remove(Clientes $entity, bool $flush = true): void
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

        $query = $this->createQueryBuilder('c')
                      ->innerJoin(Municipio::class,'m', Join::WITH,   'm.id = c.municipio');
        if($options['search']){
            $shearch = '%'.$options['search'].'%';
            $query ->andWhere('c.razonSocial like :val OR c.correo like :val OR m.nombre like :val OR c.nit like :val')
            ->setParameters(['val'=>$shearch]);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

            $actions= '<a  class="btn waves-effect waves-light btn-info" href="/clientes/'.$item->getId().'/edit">editar</a>';
           

            
            $list[] = ['razonSocial'=>$item->getRazonSocial(),
                       'tipoDocumento'=>$item->getTipoDocumento(),
                       'numeroDocumento'=>$item->getNit(),
                       'telefono'=>$item->getTelefono(),
                       'correo'=>$item->getCorreo(),
                       'actions'=> $actions];
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
            $query ->where('c.razonSocial like :val  OR c.nit like :val')
            ->setParameter('val',$shearch);
        }
       
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

           
           

            
            $list[] = [
                'id' => $item->getId(),       
                'text'=>$item->getRazonSocial(),
                'tipo_documento'=>$item->getTipoDocumento(),
                'numero_identificacion'=>$item->getNit(),
                'regimen' => $item->getRegimen(),
                'obligacion' => $item->getTaxLevelCode(),
                'direccion'  =>$item->getDireccion(),
                'municipio'  => $item->getMunicipio()->getId(),
            ];
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
     

    }

    // /**
    //  * @return Clientes[] Returns an array of Clientes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Clientes
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
