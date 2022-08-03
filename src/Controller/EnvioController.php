<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Form\EnvioType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EnvioRepository;
use App\Repository\TarifasRepository;
use App\Entity\TarifasConfiguracion;
use App\Repository\PaisRepository;
use App\Repository\PaisZonaRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/envio')]
class EnvioController extends AbstractController
{
    #[Route('/', name: 'app_envio_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('envio/index.html.twig', []);
    }
    #[Route('/lista_envios_retrasados', name: 'app_envio_index_retrasados', methods: ['GET'])]
    public function listaRetrasados(EntityManagerInterface $entityManager): Response
    {


        return $this->render('envio/listado_retrasados.html.twig', []);
    }

    #[Route('/actualizarvalor', name: 'app_envio_actualizarvalor', methods: ['GET', 'POST'])]
    public function actualizarvalor(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository,  PaisZonaRepository $paisZonaRepository): Response
    {

        $envio = $entityManager
            ->getRepository(Envio::class)
            ->find($request->request->get('id'));




        if ($envio->getPesoReal() < 10) {
            if(fmod($envio->getPesoReal(), 1) != 0.5){
                $peso_real = $this->roundUp($envio->getPesoReal(), 0.5);
            }else{
                $peso_real = $envio->getPesoReal();
            }
           
        } else {
            $peso_real = ceil($envio->getPesoReal());
        }

        if ($envio->getPaisOrigen()->getCode() == 'CO') {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisDestino()->getId(), 'tipo' => 'exportacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' => $peso_real]);
        } else {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisOrigen()->getId(), 'tipo' => 'importacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' => $peso_real]);
        }

        $envio->setTotalPesoCobrar($peso_real);
        $envio->setTotalACobrar($tarifa[0]['total']);




        $entityManager->persist($envio);
        $entityManager->flush();

        $costo = array('costo' => $envio->getTotalACobrar());


        return $this->json($costo);
    }

    #[Route('/actualizar_valor_especial_real', name: 'app_envio_actualizar_valor_especial_real', methods: ['GET', 'POST'])]
    public function actualizarvalorEspecialReal(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository,  PaisZonaRepository $paisZonaRepository): Response
    {

        $envio = $entityManager
            ->getRepository(Envio::class)
            ->find($request->request->get('id'));

            if ($envio->getPesoReal() < 1) {
                if(fmod($envio->getPesoReal(), 1) != 0.5){
                    $peso_real = $this->roundUp($envio->getPesoReal(), 0.5);
                }else{
                    $peso_real = $envio->getPesoReal();
                }
               
            } else {
                $peso_real = ceil($envio->getPesoReal());
            }

            $peso_real =  ceil($envio->getPesoReal());


        if ($envio->getPaisOrigen()->getCode() == 'CO') {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisDestino()->getId(), 'tipo' => 'especial_exportacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' => $peso_real]);
        } else {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisOrigen()->getId(), 'tipo' => 'especial_importacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' => $peso_real]);
        }

        $envio->setTotalPesoCobrar($peso_real);
        $envio->setTotalACobrar($tarifa[0]['total']);




        $entityManager->persist($envio);
        $entityManager->flush();

        $costo = array('costo' => $envio->getTotalACobrar());


        return $this->json($costo);
    }

    #[Route('/actualizar_valor_especial', name: 'app_envio_actualizar_valor_especial', methods: ['GET', 'POST'])]
    public function actualizarvalorEspecial(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository,  PaisZonaRepository $paisZonaRepository): Response
    {

        $envio = $entityManager
            ->getRepository(Envio::class)
            ->find($request->request->get('id'));
        
            if ($envio->getTotalPesoCobrar() < 1) {
                if(fmod($envio->getTotalPesoCobrar(), 1) != 0.5){
                    $peso_cobrar = $this->roundUp($envio->getTotalPesoCobrar(), 0.5);
                }else{
                    $peso_cobrar = $envio->getTotalPesoCobrar();
                }
               
            } else {
                $peso_cobrar = ceil($envio->getTotalPesoCobrar());
            }     

       


        if ($envio->getPaisOrigen()->getCode() == 'CO') {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisDestino()->getId(), 'tipo' => 'especial_exportacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' =>  $peso_cobrar]);
        } else {

            $zona = $paisZonaRepository->findOneByZona(['pais' => $envio->getPaisOrigen()->getId(), 'tipo' => 'especial_importacion']);
            $tarifa = $tarifasRepository->findOneByPeso(['zona' => $zona->getZona()->getId(), 'peso' =>  $peso_cobrar]);
        }


        $envio->setTotalPesoCobrar( $peso_cobrar);
        $envio->setTotalACobrar($tarifa[0]['total']);




        $entityManager->persist($envio);
        $entityManager->flush();

        $costo = array('costo' => $envio->getTotalACobrar());


        return $this->json($costo);
    }


    #[Route('/table', name: 'app_envio_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, EnvioRepository $envioRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');
        $columns = $request->request->get('columns');
        $orderBy = [
            'column' => $columns[$request->request->get('order')[0]['column']]['data'],
            'dir' => $request->get('order')[0]['dir'],
        ];



        $data_table  = $envioRepository->findByDataTable(['page' => ($start / $length), 'pageSize' => $length, 'search' => $search['value'], 'order' => $orderBy]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }
    //envios retrasados
    #[Route('/table_retrasos', name: 'app_envio_table_retrasos', methods: ['GET', 'POST'])]
    public function tableRetrasos(Request $request, EntityManagerInterface $entityManager, EnvioRepository $envioRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        $columns = $request->request->get('columns');
        $orderBy = [
            'column' => $columns[$request->request->get('order')[0]['column']]['data'],
            'dir' => $request->get('order')[0]['dir'],
        ];

        $fecha = new DateTime();

        $data_table  = $envioRepository->findByDataTableRetrasos(['page' => ($start / $length), 'pageSize' => $length, 'search' => $search['value'], "fecha" => $fecha->format('Y-m-d'), 'order' => $orderBy]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data'],
        );


        return $this->json($responseData);
    }
    //listado de envios sin facturar

    #[Route('/listado_envios', name: 'app_envio_listado_envios', methods: ['GET', 'POST'])]
    public function listadoEnvios(Request $request, EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $shearch = '%' . $request->request->get('envia') . '%';
        $query = $entityManager->getRepository(Envio::class)->createQueryBuilder('e');

        if ($request->request->get('fecha_inicio')) {

            $query->andWhere('e.fechaEnvio >= :val')
                ->setParameter('val', $request->request->get('fecha_inicio'))
                ->andWhere('e.fechaEnvio <= :val1')
                ->setParameter('val1', $request->request->get('fecha_fin'));
        }

        if ($request->request->get('envia')) {
            $query->andWhere('e.quienEnvia like :val2')
                ->setParameter('val2', $shearch);
        }
        $envios = $query->andWhere('e.facturado = 0')
            ->setMaxResults(200)
            ->getQuery()->getResult();



        return $this->render('envio/listado_envios.html.twig', [
            'envios'     => $envios,
            'factura_id' => $request->request->get('factura_id')
        ]);
    }

    #[Route('/new', name: 'app_envio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $envio = new Envio();
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);
        if($request->query->get('url')){
            $url = $request->query->get('url');
        }else{
            $url = 'app_envio_index';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($envio);
            $entityManager->flush();

            return $this->redirectToRoute('app_envio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/new.html.twig', [
            'envio' => $envio,
            'form' => $form,
            'url'  =>$url,
        ]);
    }

    #[Route('/{id}/mostrar', name: 'app_envio_show', methods: ['GET'])]
    public function show(Envio $envio): Response
    {
        return $this->render('envio/show.html.twig', [
            'envio' => $envio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_envio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Envio $envio, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);
        
        if($request->query->get('url')){
            $url = $request->query->get('url');
        }else{
            $url = 'app_envio_index';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Envio Actualizado Correctamente'
            );

            return $this->redirectToRoute($url, [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/edit.html.twig', [
            'envio' => $envio,
            'form' => $form,
            'url'  =>$url,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_envio_delete', methods: ['GET'])]
    public function delete(Request $request, Envio $envio, EntityManagerInterface $entityManager): Response
    {


        if ($envio->getFacturaItems() == null) {
            $entityManager->remove($envio);
            $entityManager->flush();
        } else {
            $this->addFlash(
                'notice',
                'No se puede eliminar este envío porque ya se encuentra facturado'
            );
        }


        return $this->redirectToRoute('app_integracion_dhl', [], Response::HTTP_SEE_OTHER);
    }
    public function roundUp($number, $nearest)
    {
        return $number + ($nearest - fmod($number, $nearest));
    }
}
