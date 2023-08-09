<?php

namespace App\Repository;

use App\Entity\Clientes;
use App\Entity\Departamento;
use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use App\Entity\Municipio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EnviosNacionales>
 *
 * @method EnviosNacionales|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnviosNacionales|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnviosNacionales[]    findAll()
 * @method EnviosNacionales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnviosNacionalesRepository extends ServiceEntityRepository
{   
    private $entityManager; 

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {   
        $this->entityManager = $entityManager;
        parent::__construct($registry, EnviosNacionales::class);
    }

    public function add(EnviosNacionales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EnviosNacionales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDataTable(array $options = [])
    {

        $currentPage = isset($options['page']) ? $options['page'] : 0;
        $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 10;

        $query = $this->createQueryBuilder('e')
            ->innerJoin(Municipio::class, 'm', Join::WITH,   'm.id = e.municipioDestino')
            ->innerJoin(Departamento::class, 'd', Join::WITH, 'm.departamento = d.id')
            ->innerJoin(Municipio::class, 'm1', Join::WITH,  'm1.id = e.municipioOrigen')
            ->innerJoin(Departamento::class, 'd1', Join::WITH, 'm1.departamento = d1.id')
            ->innerJoin(Clientes::class,'c' , Join::WITH,  'c.id = e.cliente')
            ->innerJoin(EnviosNacionalesUnidades::class,'enu' , Join::WITH,  'e.id = enu.envioNacional');
        if ($options['search']) {
            $shearch = '%' . $options['search'] . '%';
            $query->andWhere('e.numero like :val OR e.fecha like :val  OR c.razonSocial like :val OR c.nit like :val OR e.destinatario like :val OR e.estado like :val OR m.nombre like :val OR m1.nombre like :val OR enu.numeroGuia like :val OR  d.nombre like :val OR  d1.nombre like :val')
                ->setParameter('val', $shearch);
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
            if ($item->getFacturaItems()) {

                $actions = '<a class="btn btn-warning" title="'.$item->getFacturaItems()->getFacturaClientes()->getFacturaResolucion()->getPrefijo().'-'. $item->getFacturaItems()->getFacturaClientes()->getNumeroFactura().'" href="/impresion/impresion_factura?id='.$item->getFacturaItems()->getFacturaClientes()->getId().'" target="_blank"> <i class="fa fa-qrcode"  title="'.$item->getFacturaItems()->getFacturaClientes()->getFacturaResolucion()->getPrefijo().'-'. $item->getFacturaItems()->getFacturaClientes()->getNumeroFactura().'" ></i></a>';
            }else{
                $actions = '<a  class="btn waves-effect waves-light btn-warning" href="/envios_nacionales/' . $item->getId() . '/edit"><i class="fas fa-pencil-alt"></i></a>';
                $actions .= '<a  class="btn waves-effect waves-light btn-danger" href="/envios_nacionales/' . $item->getId() . '/delete" onclick="return confirm(\'Estas seguro de borrar esta remisión\')"><i class="fas fa-trash-alt"></i></a>';
            }
           
           // $actions .= '<a  class="btn waves-effect waves-light btn-danger" href="/envios_nacionales/' . $item->getId() . '/delete" onclick="return confirm(\'Estas seguro de borrar este envio\')"><i class="fas fa-trash-alt"></i></a>';
            
           $items = $this->entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('en')
           ->andWhere('en.envioNacional= :val')
           ->setParameter('val', $item->getId())
           ->getQuery()->getResult();

           
           $guias = '<select class="copiable form-control" onclick="copyToClipboard(this)">';
           foreach($items as $unidad){
            $guia = strlen($unidad->getNumeroGuia())> 13  ? substr($unidad->getNumeroGuia(), 0, 12): $unidad->getNumeroGuia();
            
            $guias.= '<option value="'.$guia.'">'.$guia.'</option>';
           }
           $actions .= '<a  class="btn waves-effect waves-light btn-info" href="/impresion/impresion_remision?id='.$item->getId().'" title="Imprimir"><span class="fas fa-print"></span></a>';
           $actions .= '&nbsp;<input name="envId[]" id="checkBoxImprimir" value="'.$item->getId().'" type="checkbox">';
           /* $actions.='<a class="icon-select"  style="position:relative; float:right;cursor:pointer;" onMouseOver="verEnvio('.$item->getId().');" onMouseOut ="ocultarEnvio()" title="Ver Envio">
                         <i class="fa fa-eye text-success" ></i>
                     </a>';*/

                     $select ='<select class="form-select" data-id="'.$item->getId().'">';
                     $select .='<option value= ""> </option>';

                     $estados = array(
                        "transito"=> "transito",
                        "entregado"=> "entregado");

                    $colores = array(
                        "transito"=> '#FFEEBA',
                        "entregado"=> '#C3E6CB');
                    $color = '#FFFFFF';
                     foreach ($estados as $key => $value){
                        
                         if($key == $item->getEstado()){
                             $select .='<option value= "'.$key.'" selected="selected">'.$value.' </option>';
                             $color = $colores[$key];
         
                         }else{
                             $select .='<option value= "'.$key.'">'.$value.' </option>';
                         }
         
                     }
                     $select .='</select>';

            $list[] = [
                'numero' =>  $item->getNumero().'<br>'.$guias.'</select>',
                'valorTotal' => $item->getValorTotal(),
                'fecha' => $item->getFecha()->format('Y-m-d'),
                'cliente' => $item->getCliente()->getRazonSocial(),
                'destinatario' => $item->getDestinatario(),
                'municipioDestino' => $item->getMunicipioDestino()->getNombre(),
                'estado' => $select,
                'actions' => $actions,
                'color' => $color
            ];
            // echo $item->getZona()->getNombre();
        }
        return ['data' => $list, 'totalRecords' => $totalItems];
    }
}
