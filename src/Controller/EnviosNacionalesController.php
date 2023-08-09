<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\Departamento;
use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use App\Entity\Municipio;
use App\Form\ClientesType;
use App\Form\EnviosNacionalesType;
use App\Repository\EnviosNacionalesRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/envios_nacionales')]
class EnviosNacionalesController extends AbstractController
{
    #[Route('/', name: 'app_envios_nacionales_index', methods: ['GET'])]
    public function index(EnviosNacionalesRepository $enviosNacionalesRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('envios_nacionales/index.html.twig', [
            'envios_nacionales' => $enviosNacionalesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_envios_nacionales_new', methods: ['GET', 'POST'])]
    public function new(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $enviosNacionale = new EnviosNacionales();
        $numeroQuery = $entityManager->getRepository(EnviosNacionales::class)->createQueryBuilder('e')
                ->orderBy('e.numero', 'DESC')
                ->setMaxResults(1);

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $enviosNacionale->setNumero($consulta->getNumero() + 1);
            } else {
                $enviosNacionale->setNumero(1);
            }
            $razon_social = '';
        if($request->query->get('id_anterior')){
            $envioAnterior = $entityManager->getRepository(EnviosNacionales::class)->find($request->query->get('id_anterior'));
            $enviosNacionale->setCliente($envioAnterior->getCliente());
            $enviosNacionale->setDireccionOrigen($envioAnterior->getCliente()->getDireccion());
            $enviosNacionale->setMunicipioOrigen($envioAnterior->getCliente()->getMunicipio());
            $razon_social = $envioAnterior->getCliente()->getRazonSocial();

        }
        $enviosNacionale->setObservacion('LLAMAR AL REMITENTE ANTES DE REALIZAR LA DEVOLUCIÓN');
        $timezone = new DateTimeZone('America/Bogota');
        $fecha = new DateTime('now', $timezone);
        $enviosNacionale->setFecha($fecha);
        $form = $this->createForm(EnviosNacionalesType::class, $enviosNacionale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numeroQuery = $entityManager->getRepository(EnviosNacionales::class)->createQueryBuilder('e')
            ->orderBy('e.numero', 'DESC')
            ->setMaxResults(1);

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $enviosNacionale->setNumero($consulta->getNumero() + 1);
            } else {
                $enviosNacionale->setNumero(1);
            }
            $entityManager->persist($enviosNacionale);
            $entityManager->flush();

            for($i=1; $i<=$enviosNacionale->getUnidades(); $i++ ){
                $numeroQuery = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('e')
                ->orderBy('e.numeroReferencia', 'DESC')
                ->setMaxResults(1);

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $numero = $consulta->getNumeroReferencia() + 1;
            } else {
                $numero = 1;
            }
                $referenciaUnida = new EnviosNacionalesUnidades();
                $referenciaUnida->setPeso($enviosNacionale->getPeso()/$enviosNacionale->getUnidades());
                $referenciaUnida->setValorDeclarado($enviosNacionale->getSeguro()/$enviosNacionale->getUnidades());
                $referenciaUnida->setNumeroReferencia($numero);
                $referenciaUnida->setLargo(35);
                $referenciaUnida->setAlto(50);
                $referenciaUnida->setAncho(50);
                $referenciaUnida->setNumeroGuia(0);
                $referenciaUnida->setEnvioNacional($enviosNacionale);
                $entityManager->persist($referenciaUnida);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_envios_nacionales_new', ['id_anterior' => $enviosNacionale->getId()], Response::HTTP_SEE_OTHER);
           
        }

        return $this->renderForm('envios_nacionales/new.html.twig', [
            'envios_nacionale' => $enviosNacionale,
            'form' => $form,
            'razon_social' =>  $razon_social
        ]);
    }

    #[Route('/create_cliente', name: 'app_envios_nacionales_create_cliente', methods: ['GET', 'POST'])]
    public function createCliente(Request $request,  EntityManagerInterface $entityManager): Response
    {
       
        $cliente = new Clientes();


        $form = $this->createForm(ClientesType::class, $cliente);
       
        $form->handleRequest($request);

        if ( $form->isValid()) {
            $entityManager->persist($cliente);
            $entityManager->flush();
           
        }
        $responseData = array(
            "id" => $cliente->getId(),
            "razon_social" => $cliente->getRazonSocial(),
            "direccion" => $cliente->getDireccion(),
            "municipio" =>  $cliente->getMunicipio()->getId(),
        );


        return $this->json($responseData);
        
    }

    #[Route('/{id}/show', name: 'app_envios_nacionales_show', methods: ['GET'])]
    public function show(EnviosNacionales $enviosNacionale): Response
    {
        return $this->render('envios_nacionales/show.html.twig', [
            'envios_nacionale' => $enviosNacionale,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_envios_nacionales_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EnviosNacionales $enviosNacionale, EnviosNacionalesRepository $enviosNacionalesRepository,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnviosNacionalesType::class, $enviosNacionale);
        $form->handleRequest($request);

        $unidades = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('eu')
            ->andWhere('eu.envioNacional = :val')
            ->setParameter('val', $enviosNacionale->getId())
            ->getQuery()->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            $enviosNacionalesRepository->add($enviosNacionale, true);

            return $this->redirectToRoute('app_envios_nacionales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envios_nacionales/edit.html.twig', [
            'envios_nacionale' => $enviosNacionale,
            'form' => $form,
            'unidades'=> $unidades
        ]);
    }

    #[Route('/{id}/agregar_unidad', name: 'app_envios_nacionales_agregar_unidad', methods: ['GET', 'POST'])]
    public function agregarUnidad(Request $request, EnviosNacionales $enviosNacionale, EnviosNacionalesRepository $enviosNacionalesRepository,EntityManagerInterface $entityManager): Response
    {   
        $numeroQuery = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('e')
                ->orderBy('e.numeroReferencia', 'DESC')
                ->setMaxResults(1);

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $numero = $consulta->getNumeroReferencia() + 1;
            } else {
                $numero = 1;
            }

        $referenciaUnida = new EnviosNacionalesUnidades();
        $referenciaUnida->setPeso(0);
        $referenciaUnida->setValorDeclarado(0);
        $referenciaUnida->setNumeroReferencia($numero);
        $referenciaUnida->setLargo(35);
        $referenciaUnida->setAlto(50);
        $referenciaUnida->setAncho(50);
        $referenciaUnida->setNumeroGuia(0);
        $referenciaUnida->setEnvioNacional($enviosNacionale);
        $entityManager->persist($referenciaUnida);
        $entityManager->flush();

        return $this->redirectToRoute('app_envios_nacionales_edit', ['id' => $enviosNacionale->getId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('/table', name: 'app_envios_nacionales_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, EnviosNacionalesRepository $envioNacionalRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');
        $columns = $request->request->get('columns');
        $orderBy = [
            'column' => $columns[$request->request->get('order')[0]['column']]['data'],
            'dir' => $request->get('order')[0]['dir'],
        ];



        $data_table  = $envioNacionalRepository->findByDataTable(['page' => ($start / $length), 'pageSize' => $length, 'search' => $search['value'], 'order' => $orderBy]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }

    #[Route('/cargar_items', name: 'app_envios_nacionales_cargar_items', methods: ['GET', 'POST'])]
    public function executeCargarItems(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $datos = json_decode($request->request->get('datos'));
        $total = 0;

        foreach ($datos as $dato) {
            if ($dato != null && $dato != '') {

              

                $item = $entityManager->getRepository(EnviosNacionalesUnidades::class)->find($request->request->get('id'));
                $item->setPeso($dato[0]->valor);
                $item->setValorDeclarado($dato[1]->valor);
                $item->setNumeroReferencia($dato[2]->valor);
                $item->setLargo($dato[3]->valor);
                $item->setAlto($dato[4]->valor);
                $item->setAncho($dato[5]->valor);
                $item->setNumeroGuia($dato[6]->valor);
                
                $entityManager->persist($item);
                $entityManager->flush();
            }
        }
        $remision = $item->getEnvioNacional();
        $items = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('en')
            ->andWhere('en.envioNacional= :val')
            ->setParameter('val', $remision->getId())
            ->getQuery()->getResult();

        $total_remision = 0;
        $total_peso = 0;
        foreach ($items as $item) {
            $total_remision = $total_remision + $item->getValorDeclarado();
            $total_peso = $total_peso+$item->getPeso();
            
        }
        $remision->setSeguro($total_remision);
        $remision->setPeso($total_peso);
        $entityManager->persist($remision);
        $entityManager->flush();


        
        $thearray[0] = $request->request->get('id');
        $thearray[1] = number_format($total_remision , 2, '.', '');


        return $this->json($thearray);
    }

    #[Route('item_delete/{id}', name: 'app_envios_nacionales_item_delete', methods: ['POST'])]
    public function itemDelete(Request $request, EnviosNacionalesUnidades $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {

            $remision = $entityManager->getRepository(EnviosNacionales::class)->find($item->getEnvioNacional()->getId());
            $remision->setUnidades($remision->getUnidades()-1);
            $remision->setSeguro($remision->getSeguro()- $item->getValorDeclarado());

            $entityManager->persist($remision);
            $entityManager->flush();

            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_envios_nacionales_edit', ['id' => $remision->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/save_estado', name: 'app_envios_nacionales_save_estado', methods: ['GET', 'POST'])]
    public function executeSaveTier(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $envioNacional= $entityManager->getRepository(EnviosNacionales::class)->find($request->request->get('id'));
       

        $envioNacional->setEstado($request->request->get('estado_id'));

        $entityManager->persist($envioNacional);
        $entityManager->flush();


        $responseData = array(
            "results" => true,
        );
        return $this->json($responseData);
    }

    //listado de envios sin facturar

    #[Route('/listado_envios', name: 'app_envios_nacionales_listado_envios', methods: ['GET', 'POST'])]
    public function listadoEnvios(Request $request, EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $shearch = '%' . $request->request->get('filtro') . '%';
        $query = $entityManager->getRepository(EnviosNacionales::class)->createQueryBuilder('e')
                            ->innerJoin(Municipio::class, 'm', Join::WITH,   'm.id = e.municipioDestino')
                            ->innerJoin(Departamento::class, 'd', Join::WITH, 'm.departamento = d.id')
                            ->innerJoin(Municipio::class, 'm1', Join::WITH,  'm1.id = e.municipioOrigen')
                            ->innerJoin(Departamento::class, 'd1', Join::WITH, 'm1.departamento = d1.id')
                            ->innerJoin(Clientes::class, 'c', Join::WITH,   'c.id = e.cliente')
                            ->innerJoin(EnviosNacionalesUnidades::class,'enu' , Join::WITH,  'e.id = enu.envioNacional');;

        if ($request->request->get('fecha_inicio')) {

            $query->andWhere('e.fecha >= :val2')
                ->setParameter('val2', $request->request->get('fecha_inicio'))
                ->andWhere('e.fecha <= :val1')
                ->setParameter('val1', $request->request->get('fecha_fin'));
        }
        

        if ($request->request->get('filtro')) {
            $query->andWhere('e.numero like :val OR e.fecha like :val   OR e.destinatario like :val OR m.nombre like :val OR m1.nombre like :val OR enu.numeroGuia like :val OR c.razonSocial like :val OR  c.nit like :val OR  d.nombre like :val OR  d1.nombre like :val')
                ->setParameter('val', $shearch);
        }
        $envios = $query->andWhere('e.facturado = 0')
            ->orderBy('e.numero', 'ASC')
            ->setMaxResults(200)
            ->getQuery()->getResult();



        return $this->render('envios_nacionales/listado_envios.html.twig', [
            'envios'     => $envios,
            'factura_id' => $request->request->get('factura_id')
        ]);
    }

    #[Route('/{id}/delete', name: 'app_envios_nacionales_delete', methods: ['GET'])]
    public function delete(Request $request, EnviosNacionales $enviosNacionale, EnviosNacionalesRepository $enviosNacionalesRepository): Response
    {   
        if ($enviosNacionale->getFacturaItems() == null) {
            $enviosNacionalesRepository->remove($enviosNacionale, true);
        } else {
            $this->addFlash(
                'notice',
                'No se puede eliminar este envío porque ya se encuentra facturado'
            );
        }
        

        return $this->redirectToRoute('app_envios_nacionales_index', [], Response::HTTP_SEE_OTHER);
    }
}
