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
        $campos = array(
            "fecha",
            "numeroFactura",
            "cliente",
            "total"
        );

       

        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;
         

        $query = $this->createQueryBuilder('f')
            ->innerJoin(FacturaResolucion::class, 'fr', Join::WITH,   'fr.id = f.facturaResolucion')
            ->innerJoin(Clientes::class, 'c', Join::WITH,  'c.id = f.cliente');
           
        if($options['nacional'] != 0){
           
            $query->andWhere('f.tipoFactura = :tipo')
                ->setParameter('tipo', 'FACTURA_VENTA_NACIONAL');
        }
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('f.numeroFactura like :val OR f.fecha like :val OR fr.prefijo like :val OR c.razonSocial like :val OR c.nit like :val')
                ->setParameter('val',$shearch);
        }
        $query->andWhere('fr.empresa  = :empresa')
                ->setParameter('empresa',$options['company']);

        // Procesar múltiples criterios de orden
        if (!empty($options['order'])) {
            foreach ($options['order'] as $order) {
                $columnIndex = $order['column'];
                $direction = strtoupper($order['dir']) === 'ASC' ? 'ASC' : 'DESC';

                if (isset($campos[$columnIndex])) {
                    $query->addOrderBy("f.{$campos[$columnIndex]}", $direction);
                }
            }
        }

        $query->getQuery();
        $paginator = new Paginator($query);
        $totalItems = $paginator->count();
        $paginator->getQuery()->setFirstResult($pageSize * $currentPage)->setMaxResults($pageSize)->getResult();
        $list = [];
        foreach ($paginator as $factura) {

            $actions = '';
            $reportar = '';

            
            $actions .= '<a  class="btn waves-effect waves-light btn-info" href="/impresion/impresion_factura?id=' . $factura->getId() . '" title="Imprimir" target="_blank"><span class="fas fa-print"></span></a>';
            
            
            $actions .= "<button onclick='mostrarNotasCredito(" . $factura->getId() . ");'  class='btn btn-info'>NC</button>";
            if ($factura->getFacturado()) {
                $actions .= '<button class="btn btn-success" id="desverificar'.$factura->getId().'" onClick="desverificar('.$factura->getId().');"> <i class="fas fa-check" ></i></button>';
            }else {
                $actions .= '<button class="btn btn-secondary" id="verificar'.$factura->getId().'" onClick="verificar('.$factura->getId().');"> <i class="fas fa-check" ></i></button>';
            }
            if ($factura->getCufe() == '' || $factura->getCufe() == null) {

                
                $reportar = '<button type="button" id="reportar' . $factura->getId() . '" class="btn" onclick="Reportar(' . $factura->getId() . ');"title="Reportar Dian"><img src="/assets/images/facturas/dian.png" height="30px" width="30px"></button>';
                  
                if ($factura->getTipoFactura() == 'FACTURA_VENTA') {

                    $actions .= '<a  class="btn waves-effect waves-light btn-warning" href="/factura/' . $factura->getId() . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                } else if ($factura->getTipoFactura() == 'FACTURA_VENTA_NACIONAL') {
                    $actions .= '<a  class="btn waves-effect waves-light btn-warning" href="/factura_nacionales/' . $factura->getId() . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                }  else {
                    $actions .= '<a  class="btn waves-effect waves-light btn-warning" href="/factura_simple/' . $factura->getId() . '/edit"><span class="fas fa-pencil-alt"></span></a>';
                }


                //$actions .= "<button onclick='mostrarNotasDebito(".$factura->getId().");'  class='btn btn-success'>ND</button>";
                if ($factura->getRespuestaDian() != '' || $factura->getRespuestaDian() != null) {
                    $actions .= '<a class="icon-select"  style="position:relative; float:right;cursor:pointer;" onClick="verErrores(' . $factura->getId() . ');" title="Ver respuesta Dian">
                         <i class="fas fa-code text-success" ></i>
                     </a>';
                }
            }
            if($factura->getSoportePago()){
                $soporte_pago= '<input type="file"  name="archivo_excel" data-id="'.$factura->getId().'" class="form-control file_forma_de_pago"/>  ';
                $soporte_pago.='<a href="/uploads/images/'.$factura->getSoportePago().'" class="btn btn-dark" target="_blank">
                <i class="fa fa-clipboard"></i>'.$factura->getSoportePago().'
            </a>';
            }else{
                $soporte_pago= '<input type="file" id="archivo_excel" data-id="'.$factura->getId().'" name="archivo_excel" class="form-control file_forma_de_pago"/>';
            }


            $list[] = [
                'fecha' => $factura->getFecha()->format('Y-m-d'),
                'numero' => $factura->getFacturaResolucion()->getPrefijo() . $factura->getNumeroFactura(),
                'cliente' => $factura->getCliente()->getRazonSocial() . '(' . $factura->getCliente()->getNit() . ')',
                'total' => $factura->getTotal(),
                'actions' => $reportar . $actions,
                'soporte_pago' =>$soporte_pago
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }



    public function CuerpoReporte($value): ?array
    {
        $cuerpo_json = array();



        return $cuerpo_json;
    }
}
