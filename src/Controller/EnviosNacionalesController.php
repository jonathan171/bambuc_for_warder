<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use App\Form\ClientesType;
use App\Form\EnviosNacionalesType;
use App\Repository\EnviosNacionalesRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $form = $this->createForm(EnviosNacionalesType::class, $enviosNacionale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                $referenciaUnida->setPeso(0);
                $referenciaUnida->setValorDeclarado($enviosNacionale->getSeguro()/$enviosNacionale->getUnidades());
                $referenciaUnida->setNumeroReferencia($numero);
                $referenciaUnida->setLargo(0);
                $referenciaUnida->setAlto(0);
                $referenciaUnida->setAncho(0);
                $referenciaUnida->setNumeroGuia(0);
                $referenciaUnida->setEnvioNacional($enviosNacionale);
                $entityManager->persist($referenciaUnida);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_envios_nacionales_edit', ['id' => $enviosNacionale->getId()], Response::HTTP_SEE_OTHER);
           
        }

        return $this->renderForm('envios_nacionales/new.html.twig', [
            'envios_nacionale' => $enviosNacionale,
            'form' => $form,
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
            "razon_social" => $cliente->getRazonSocial()
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
        $referenciaUnida->setLargo(0);
        $referenciaUnida->setAlto(0);
        $referenciaUnida->setAncho(0);
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
        
        foreach ($items as $item) {
            $total_remision = $total_remision + $item->getValorDeclarado();
            
        }
        $remision->setSeguro($total_remision);
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

    #[Route('/{id}', name: 'app_envios_nacionales_delete', methods: ['POST'])]
    public function delete(Request $request, EnviosNacionales $enviosNacionale, EnviosNacionalesRepository $enviosNacionalesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enviosNacionale->getId(), $request->request->get('_token'))) {
            $enviosNacionalesRepository->remove($enviosNacionale, true);
        }

        return $this->redirectToRoute('app_envios_nacionales_index', [], Response::HTTP_SEE_OTHER);
    }
}