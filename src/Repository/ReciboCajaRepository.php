<?php

namespace App\Repository;

use App\Entity\ReciboCaja;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<ReciboCaja>
 *
 * @method ReciboCaja|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReciboCaja|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReciboCaja[]    findAll()
 * @method ReciboCaja[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReciboCajaRepository extends ServiceEntityRepository
{   
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, ReciboCaja::class);
        $this->security = $security;
    }

    public function add(ReciboCaja $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReciboCaja $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findLastNumeroRecibo(): ?int
    {
        try {
            return $this->createQueryBuilder('r')
                ->select('r.numero_recibo')
                ->orderBy('r.numero_recibo', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null; // Si no hay registros, devuelve null
        }
    }

    public function findByDataTable(array $options = [])
    {
        $campos = array(
            "fecha",
            "numero",
            "total"
        );

       

        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;
         

        $query = $this->createQueryBuilder('r');
         
           
        
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('r.numero_recibo like :val OR r.fecha like :val ')
                ->setParameter('val',$shearch);
        }
       
        $query->orderBy("r.{$campos[$options['order'][0]['column']]}", "{$options['order'][0]['dir']}");

        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $recibo) {

            $actions = '';
            $reportar = '';

            
            $actions .= '<a  class="btn waves-effect waves-light btn-info" href="/impresion/impresion_recibo?id=' . $recibo->getId() . '" title="Imprimir" target="_blank"><span class="fas fa-print"></span></a>';
            
            if( !$recibo->getFirma()){
                $actions .= '<a  class="btn waves-effect waves-light btn-warning" href="/recibo_caja/' . $recibo->getId() . '/edit"><span class="fas fa-pencil-alt"></span></a>';
            }
          if ($this->security->isGranted('ROLE_FIRMA')) {
                $actions .= '<button  class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#firmaModal" data-recibo-id="'. $recibo->getId().'">
                    Firmar
                </button>';
            }

            $list[] = [
                'fecha' => $recibo->getFecha()->format('Y-m-d'),
                'numero' => 'RE'. $recibo->getNumeroRecibo(),
                'total' => $recibo->getTotal(),
                'actions' => $actions,
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }

}
