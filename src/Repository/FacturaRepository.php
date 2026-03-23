<?php

namespace App\Repository;

use App\Entity\Clientes;
use App\Entity\Factura;
use App\Entity\FacturaResolucion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Factura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factura[]    findAll()
 * @method Factura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factura::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Factura $entity, bool $flush = true): void
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
    public function remove(Factura $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Factura[] Returns an array of Factura objects
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
        $pageSize = $options['pageSize'] ?? 10;

        $qb = $this->createQueryBuilder('f')
            ->select([
                'f.id AS id',
                'f.fecha AS fecha',
                'f.numeroFactura AS numeroFactura',
                'f.total AS total',
                'f.cufe AS cufe',
                'f.tipoFactura AS tipoFactura',
                'f.facturado AS facturado',
                'f.respuestaDian AS respuestaDian',
                'f.soportePago AS soportePago',
                'fr.prefijo AS prefijo',
                'c.razonSocial AS clienteRazonSocial',
                'c.nit AS clienteNit',
            ])
            ->innerJoin('f.facturaResolucion', 'fr')
            ->innerJoin('f.cliente', 'c');

        if (!empty($options['nacional']) && $options['nacional'] != 0) {
            $qb->andWhere('f.tipoFactura = :tipo')
            ->setParameter('tipo', 'FACTURA_VENTA_NACIONAL');
        }

        if (!empty($options['search'])) {
            $search = '%' . $options['search'] . '%';
            $qb->andWhere('
                f.numeroFactura LIKE :search OR
                f.fecha LIKE :search OR
                fr.prefijo LIKE :search OR
                c.razonSocial LIKE :search OR
                c.nit LIKE :search
            ')
            ->setParameter('search', $search);
        }

        $qb->andWhere('fr.empresa = :empresa')
        ->setParameter('empresa', $options['company']);

        if (!empty($options['order'])) {
            foreach ($options['order'] as $order) {
                $columnIndex = (int) $order['column'];
                $direction = strtoupper($order['dir']) === 'ASC' ? 'ASC' : 'DESC';

                if (isset($campos[$columnIndex])) {
                    $qb->addOrderBy($campos[$columnIndex], $direction);
                }
            }
        } else {
            $qb->addOrderBy('f.fecha', 'DESC');
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(f.id)');
        $totalItems = (int) $countQb->getQuery()->getSingleScalarResult();

        $rows = $qb->setFirstResult($pageSize * $currentPage)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getArrayResult();

        $list = [];

        foreach ($rows as $factura) {
            $actions = '';
            $reportar = '';

            $actions .= '<a class="btn waves-effect waves-light btn-info" href="/impresion/impresion_factura?id=' . $factura['id'] . '" title="Imprimir" target="_blank"><span class="fas fa-print"></span></a>';

            $actions .= "<button onclick='mostrarNotasCredito(" . $factura['id'] . ");' class='btn btn-info'>NC</button>";

            if (!empty($factura['facturado'])) {
                $actions .= '<button class="btn btn-success" id="desverificar'.$factura['id'].'" onClick="desverificar('.$factura['id'].');"><i class="fas fa-check"></i></button>';
            } else {
                $actions .= '<button class="btn btn-secondary" id="verificar'.$factura['id'].'" onClick="verificar('.$factura['id'].');"><i class="fas fa-check"></i></button>';
            }

            if (empty($factura['cufe'])) {
                $reportar = '<button type="button" id="reportar' . $factura['id'] . '" class="btn" onclick="Reportar(' . $factura['id'] . ');" title="Reportar Dian"><img src="/assets/images/facturas/dian.png" height="30" width="30"></button>';

                if ($factura['tipoFactura'] === 'FACTURA_VENTA') {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura/' . $factura['id'] . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                } elseif ($factura['tipoFactura'] === 'FACTURA_VENTA_NACIONAL') {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura_nacionales/' . $factura['id'] . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                } else {
                    $actions .= '<a class="btn waves-effect waves-light btn-warning" href="/factura_simple/' . $factura['id'] . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                }

                if (!empty($factura['respuestaDian'])) {
                    $actions .= '<a class="icon-select" style="position:relative; float:right;cursor:pointer;" onClick="verErrores(' . $factura['id'] . ');" title="Ver respuesta Dian">
                        <i class="fas fa-code text-success"></i>
                    </a>';
                }
            }

            if (!empty($factura['soportePago'])) {
                $soporte_pago  = '<input type="file" name="archivo_excel" data-id="'.$factura['id'].'" class="form-control file_forma_de_pago"/>';
                $soporte_pago .= '<a href="/uploads/images/'.$factura['soportePago'].'" class="btn btn-dark" target="_blank">
                    <i class="fa fa-clipboard"></i>'.$factura['soportePago'].'
                </a>';
            } else {
                $soporte_pago = '<input type="file" id="archivo_excel" data-id="'.$factura['id'].'" name="archivo_excel" class="form-control file_forma_de_pago"/>';
            }

            $list[] = [
                'fecha' => $factura['fecha'] instanceof \DateTimeInterface ? $factura['fecha']->format('Y-m-d') : $factura['fecha'],
                'numero' => $factura['prefijo'] . $factura['numeroFactura'],
                'cliente' => $factura['clienteRazonSocial'] . ' (' . $factura['clienteNit'] . ')',
                'total' => $factura['total'],
                'actions' => $reportar . $actions,
                'soporte_pago' => $soporte_pago,
            ];
        }

        return [
            'data' => $list,
            'totalRecords' => $totalItems,
        ];
    }



    public function CuerpoReporte($value): ?array
    {
        $cuerpo_json = array();



        return $cuerpo_json;
    }
}
