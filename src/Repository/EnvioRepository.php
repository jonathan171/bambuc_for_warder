<?php

namespace App\Repository;

use App\Entity\Envio;
use App\Entity\Factura;
use App\Entity\FacturaItems;
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
        $campos = [
            0 => 'f.fecha',
            1 => 'f.numeroFactura',
            2 => 'c.razonSocial',
            3 => 'f.total',
        ];

        $currentPage = $options['page'] ?? 0;
        $pageSize    = $options['pageSize'] ?? 10;
        $search      = trim($options['search'] ?? '');
        $company     = $options['company'] ?? null;
        $nacional    = $options['nacional'] ?? null;

        $qb = $this->createQueryBuilder('f')
            ->select([
                'f.id AS id',
                'f.fecha AS fecha',
                'f.numeroFactura AS numeroFactura',
                'f.total AS total',
                'f.cufe AS cufe',
                'f.tipoFactura AS tipoFactura',
                'f.facturado AS facturado',
                'f.soportePago AS soportePago',
                'fr.prefijo AS prefijo',
                'c.razonSocial AS clienteRazonSocial',
                'c.nit AS clienteNit',
                "(CASE 
                    WHEN f.respuestaDian IS NOT NULL AND f.respuestaDian <> '' 
                    THEN 1 ELSE 0 
                END) AS tieneRespuestaDian",
            ])
            ->innerJoin('f.facturaResolucion', 'fr')
            ->innerJoin('f.cliente', 'c');

        if ($nacional != 0 && $nacional !== null && $nacional !== '') {
            $qb->andWhere('f.tipoFactura = :tipo')
            ->setParameter('tipo', 'FACTURA_VENTA_NACIONAL');
        }

        if ($search !== '') {
            $like = '%' . $search . '%';

            $qb->andWhere('
                CAST(f.numeroFactura AS string) LIKE :search OR
                fr.prefijo LIKE :search OR
                c.razonSocial LIKE :search OR
                c.nit LIKE :search
            ')
            ->setParameter('search', $like);
        }

        if ($company !== null && $company !== '') {
            $qb->andWhere('fr.empresa = :empresa')
            ->setParameter('empresa', $company);
        }

        if (!empty($options['order'])) {
            foreach ($options['order'] as $order) {
                $columnIndex = (int) ($order['column'] ?? -1);
                $direction   = strtoupper($order['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

                if (isset($campos[$columnIndex])) {
                    $qb->addOrderBy($campos[$columnIndex], $direction);
                }
            }
        } else {
            $qb->addOrderBy('f.fecha', 'DESC')
            ->addOrderBy('f.numeroFactura', 'DESC');
        }

        $countQb = $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->innerJoin('f.facturaResolucion', 'fr')
            ->innerJoin('f.cliente', 'c');

        if ($nacional != 0 && $nacional !== null && $nacional !== '') {
            $countQb->andWhere('f.tipoFactura = :tipo')
                    ->setParameter('tipo', 'FACTURA_VENTA_NACIONAL');
        }

        if ($search !== '') {
            $like = '%' . $search . '%';

            $countQb->andWhere('
                CAST(f.numeroFactura AS string) LIKE :search OR
                fr.prefijo LIKE :search OR
                c.razonSocial LIKE :search OR
                c.nit LIKE :search
            ')
            ->setParameter('search', $like);
        }

        if ($company !== null && $company !== '') {
            $countQb->andWhere('fr.empresa = :empresa')
                    ->setParameter('empresa', $company);
        }

        $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

        $rows = $qb->setFirstResult($pageSize * $currentPage)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getArrayResult();

        $list = [];

        foreach ($rows as $factura) {
            $id = (int) $factura['id'];

            $actions = '';
            $reportar = '';

            $actions .= '<a class="btn waves-effect waves-light btn-info" href="/impresion/impresion_factura?id=' . $id . '" title="Imprimir" target="_blank">
                <span class="fas fa-print"></span>
            </a>';

            $actions .= "<button onclick='mostrarNotasCredito(" . $id . ");' class='btn btn-info'>NC</button>";

            if (!empty($factura['facturado'])) {
                $actions .= '<button class="btn btn-success" id="desverificar'.$id.'" onClick="desverificar('.$id.');">
                    <i class="fas fa-check"></i>
                </button>';
            } else {
                $actions .= '<button class="btn btn-secondary" id="verificar'.$id.'" onClick="verificar('.$id.');">
                    <i class="fas fa-check"></i>
                </button>';
            }

            if (empty($factura['cufe'])) {
                $reportar = '<button type="button" id="reportar' . $id . '" class="btn" onclick="Reportar(' . $id . ');" title="Reportar Dian">
                    <img src="/assets/images/facturas/dian.png" height="30" width="30">
                </button>';

                if ($factura['tipoFactura'] === 'FACTURA_VENTA') {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura/' . $id . '/edit">
                        <span class="fas fa-pencil-alt"></span>
                    </a>';
                } elseif ($factura['tipoFactura'] === 'FACTURA_VENTA_NACIONAL') {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura_nacionales/' . $id . '/edit">
                        <span class="fas fa-pencil-alt"></span>
                    </a>';
                } else {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura_simple/' . $id . '/edit">
                        <span class="fas fa-pencil-alt"></span>
                    </a>';
                }

                if (!empty($factura['tieneRespuestaDian'])) {
                    $actions .= '<a class="icon-select" style="position:relative; float:right;cursor:pointer;" onClick="verErrores(' . $id . ');" title="Ver respuesta Dian">
                        <i class="fas fa-code text-success"></i>
                    </a>';
                }
            }

            // Mantengo tu comportamiento actual, pero esto sería ideal moverlo a modal
            if (!empty($factura['soportePago'])) {
                $soporte_pago  = '<input type="file" name="archivo_excel" data-id="'.$id.'" class="form-control file_forma_de_pago"/>';
                $soporte_pago .= '<a href="/uploads/images/'.$factura['soportePago'].'" class="btn btn-dark" target="_blank">
                    <i class="fa fa-clipboard"></i> '.$factura['soportePago'].'
                </a>';
            } else {
                $soporte_pago = '<input type="file" id="archivo_excel" data-id="'.$id.'" name="archivo_excel" class="form-control file_forma_de_pago"/>';
            }

            $fecha = $factura['fecha'];
            if ($fecha instanceof \DateTimeInterface) {
                $fecha = $fecha->format('Y-m-d');
            }

            $list[] = [
                'fecha'        => $fecha,
                'numero'       => $factura['prefijo'] . $factura['numeroFactura'],
                'cliente'      => $factura['clienteRazonSocial'] . ' (' . $factura['clienteNit'] . ')',
                'total'        => $factura['total'],
                'actions'      => $reportar . $actions,
                'soporte_pago' => $soporte_pago,
            ];
        }

        return [
            'data' => $list,
            'totalRecords' => $totalItems,
        ];
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

    public function getEnviosPorPaisOrigen($fechaInicio = null, $fechaFin = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('m.nombre as municipio', 'COUNT(e.id) as total', 'SUM(e.totalACobrar) as total_cobrar')
            ->join('e.municipio', 'm')
            ->groupBy('m.id')
            ->orderBy('total', 'DESC');

        if ($fechaInicio) {
            $qb->andWhere('e.fechaEnvio >= :fechaInicio')
            ->setParameter('fechaInicio', $fechaInicio);
        }

        if ($fechaFin) {
            $qb->andWhere('e.fechaEnvio <= :fechaFin')
            ->setParameter('fechaFin', $fechaFin);
        }

        return $qb->getQuery()->getResult();
    }
    
    public function getEnviosPorPaisDestino($fechaInicio = null, $fechaFin = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('p.nombre as pais', 'COUNT(e.id) as total', 'SUM(e.totalACobrar) as total_cobrar')
            ->join('e.paisDestino', 'p')
            ->groupBy('p.id')
            ->orderBy('total', 'DESC') // Ordenar por cantidad de envíos (descendente)
            ->setMaxResults(10);;
        
        if ($fechaInicio) {
            $qb->andWhere('e.fechaEnvio >= :fechaInicio')
            ->setParameter('fechaInicio', $fechaInicio);
        }
        
        if ($fechaFin) {
            $qb->andWhere('e.fechaEnvio <= :fechaFin')
            ->setParameter('fechaFin', $fechaFin);
        }

        return $qb->getQuery()->getResult();
    }

    public function getTotalesPorDia(string $fechaInicio, string $fechaFin, string $agrupacion = 'daily'): array
    {
        $conn = $this->getEntityManager()->getConnection();

        if ($agrupacion === 'daily') {
            $groupSelect = "DATE(e.fecha_envio)";
        } elseif ($agrupacion === 'weekly') {
            $groupSelect = "DATE_FORMAT(e.fecha_envio, '%x-%v')";
        } else {
            $groupSelect = "DATE_FORMAT(e.fecha_envio, '%Y-%m')";
        }

        $sql = "
            SELECT
                {$groupSelect} AS fecha,
                SUM(e.total_a_cobrar) AS total,
                SUM(CASE WHEN e.facturado = 1 THEN e.total_a_cobrar ELSE 0 END) AS total_facturado,
                SUM(CASE WHEN e.facturado_recibo = 1 THEN e.total_a_cobrar ELSE 0 END) AS total_recibo,
                SUM(CASE WHEN e.facturado = 0 AND e.facturado_recibo = 0 THEN e.total_a_cobrar ELSE 0 END) AS total_sin_cobrar
            FROM envio e
            WHERE DATE(e.fecha_envio) BETWEEN :fechaInicio AND :fechaFin
            GROUP BY fecha
            ORDER BY fecha ASC
        ";

        return $conn->executeQuery($sql, [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ])->fetchAllAssociative();
    }

    public function getEnviosPorEmpresa($fechaInicio = null, $fechaFin = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.empresa as empresa', 'COUNT(e.id) as total')
            ->groupBy('e.empresa');
        
        if ($fechaInicio) {
            $qb->andWhere('e.fechaEnvio >= :fechaInicio')
            ->setParameter('fechaInicio', $fechaInicio);
        }
        
        if ($fechaFin) {
            $qb->andWhere('e.fechaEnvio <= :fechaFin')
            ->setParameter('fechaFin', $fechaFin);
        }

        return $qb->getQuery()->getResult();
    }

    public function getRangosDePesoConConteo($fechaInicio = null, $fechaFin = null, $paisDestino = null): array
    {
        $qb = $this->createQueryBuilder('e')
        ->select(
            "CASE
                WHEN e.totalPesoCobrar <= 5 THEN '0 <= 5'
                WHEN e.totalPesoCobrar > 5 AND e.totalPesoCobrar <= 10 THEN '5 <= 10'
                WHEN e.totalPesoCobrar > 10 AND e.totalPesoCobrar <= 20 THEN '10 <= 20'
                WHEN e.totalPesoCobrar > 20 AND e.totalPesoCobrar <= 30 THEN '20 <= 30'
                WHEN e.totalPesoCobrar > 30 AND e.totalPesoCobrar <= 40 THEN '30 <= 40'
                WHEN e.totalPesoCobrar > 40 AND e.totalPesoCobrar <= 50 THEN '40 <= 50'
                ELSE 'Más de 50'
             END AS rango",
            "e.totalPesoCobrar AS peso"
        );

        if ($fechaInicio) {
            $qb->andWhere('e.fechaEnvio >= :fechaInicio')
            ->setParameter('fechaInicio', $fechaInicio);
        }

        if ($fechaFin) {
            $qb->andWhere('e.fechaEnvio <= :fechaFin')
            ->setParameter('fechaFin', $fechaFin);
        }

        if ($paisDestino) {
            $qb->andWhere('e.paisDestino = :paisDestino')
            ->setParameter('paisDestino', $paisDestino);
        }

        return $qb->getQuery()->getResult();
    }
}
