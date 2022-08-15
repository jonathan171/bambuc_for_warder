<?php

namespace App\Repository;

use App\Entity\Envio;
use App\Entity\Pais;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @method Envio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Envio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Envio[]    findAll()
 * @method Envio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnvioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envio::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Envio $entity, bool $flush = true): void
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
    public function remove(Envio $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Envio[] Returns an array of Envio objects
    //  */

    public function findByDataTable(array $options = [])
    {

        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('e')
            ->innerJoin(Pais::class, 'p', Join::WITH,   'p.id = e.paisDestino')
            ->innerJoin(Pais::class, 'p1', Join::WITH,  'p1.id = e.paisOrigen');
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('e.numeroEnvio like :val OR e.fechaEnvio like :val2 OR e.empresa like :val3 OR e.quienRecibe like :val4 OR e.quienEnvia like :val5 OR p.nombre like :val6 OR p1.nombre like :val7')
                ->setParameters(['val' => $shearch, 'val2' => $shearch, 'val3' => $shearch, 'val4' => $shearch, 'val5' => $shearch, 'val6' => $shearch, 'val7' => $shearch]);
        }
        if ($options['order']['column']) {
            $query->orderBy('e.' . $options['order']['column'], $options['order']['dir']);
        }
        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {
            $actions = '<a  class="btn waves-effect waves-light btn-info" href="/envio/' . $item->getId() . '/edit"><i class="fas fa-pencil-alt"></i></a>';
            $actions .= '<a  class="btn waves-effect waves-light btn-danger" href="/envio/' . $item->getId() . '/delete" onclick="return confirm(\'Estas seguro de borrar este envio\')"><i class="fas fa-trash-alt"></i></a>';
            if ($item->getVerificado()) {
                $actions .= '<button class="btn btn-success"> <i class="fas fa-check" ></i></button>';
            }
            if ($item->getFacturado()) {
                $actions .= '<button class="btn btn-warning"> <i class="fa fa-window-close" ></i></button>';
            }
            $actions.='<a class="icon-select"  style="position:relative; float:right;cursor:pointer;" onMouseOver="verEnvio('.$item->getId().');" onMouseOut ="ocultarEnvio()" title="Ver Envio">
                         <i class="fa fa-eye text-success" ></i>
                     </a>';
            $list[] = [
                'numeroEnvio' => $item->getNumeroEnvio(),
                'totalPesoCobrar' => $item->getTotalPesoCobrar(),
                'fechaEnvio' => $item->getFechaEnvio()->format('Y-m-d'),
                'empresa' => $item->getEmpresa(),
                'quienEnvia' => $item->getQuienEnvia(),
                'quienRecibe' => $item->getQuienRecibe(),
                'paisDestino' => $item->getPaisDestino()->getNombre(),
                'actions' => $actions
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }
    public function findByDataTableRetrasos(array $options = [])
    {

        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('e')
            ->innerJoin(Pais::class, 'p', Join::WITH,   'p.id = e.paisDestino')
            ->innerJoin(Pais::class, 'p1', Join::WITH,  'p1.id = e.paisOrigen')
            ->andWhere('e.estado != 3')
            ->andWhere('e.fechaEstimadaEntrega < :fecha')
            ->setParameter('fecha', $options['fecha']);
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('e.numeroEnvio like :val OR e.fechaEnvio like :val2 OR e.empresa like :val3 OR e.quienRecibe like :val4 OR e.quienEnvia like :val5 OR p.nombre like :val6 OR p1.nombre like :val7')
                ->setParameters(['val' => $shearch, 'val2' => $shearch, 'val3' => $shearch, 'val4' => $shearch, 'val5' => $shearch, 'val6' => $shearch, 'val7' => $shearch]);
        }
        if ($options['order']['column']) {
            $query->orderBy('e.' . $options['order']['column'], $options['order']['dir']);
        }

        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $item) {

            $actions = '<a  class="btn waves-effect waves-light btn-info" href="/envio/' . $item->getId() . '/edit"><i class="fas fa-pencil-alt"></i></a>';
            $actions .= '<a  class="btn waves-effect waves-light btn-danger" href="/envio/' . $item->getId() . '/delete" onclick="return confirm(\'Estas seguro de borrar este envio\')"><i class="fas fa-trash-alt"></i></a>';
            if ($item->getVerificado()) {
                $actions .= '<button class="btn btn-success"> <i class="fas fa-check" ></i></button>';
            }
            if ($item->getFacturado()) {
                $actions .= '<button class="btn btn-warning"> <i class="fa fa-window-close" ></i></button>';
            }
            $actions.='<a class="icon-select"  style="position:relative; float:right;cursor:pointer;"  mouseup="verEnvio('.$item->getId().');" title="Ver Envio">
                         <i class="fa fa-eye text-success" ></i>
                     </a>';

            $list[] = [
                'numeroEnvio' => $item->getNumeroEnvio(),
                'fechaEnvio' => $item->getFechaEnvio()->format('Y-m-d'),
                'empresa' => $item->getEmpresa(),
                'quienEnvia' => $item->getQuienEnvia(),
                'quienRecibe' => $item->getQuienRecibe(),
                'paisDestino' => $item->getPaisDestino()->getNombre(),
                'actions' => $actions
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }



    public function findSinEntregar()
    {
        $envios =  $this->createQueryBuilder('e')
            ->andWhere('e.estado != :val')
            ->andWhere('e.empresa = :val1')
            ->setParameters(['val' => '3', 'val1' => 'DHL'])
            ->getQuery()->getResult();

        return $envios;
    }
}
