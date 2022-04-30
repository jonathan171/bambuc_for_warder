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
use Doctrine\Persistence\ManagerRegistry;

#[Route('/envio')]
class EnvioController extends AbstractController
{
    #[Route('/', name: 'app_envio_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $envios = $entityManager
            ->getRepository(Envio::class)
            ->findAll();

        return $this->render('envio/index.html.twig', [
            'envios' => $envios,
        ]);
    }

    #[Route('/actualizarvalor', name: 'app_envio_actualizarvalor', methods: ['GET', 'POST'])]
    public function actualizarvalor(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository, ManagerRegistry $doctrine): Response
    {
        $variables = $entityManager
            ->getRepository(TarifasConfiguracion::class)
            ->find(1);
         $envio= $entityManager
            ->getRepository(Envio::class)
            ->find($request->request->get('id'));
        
        $tarifa = $tarifasRepository->findOneByPeso(['zona'=>$envio->getPaisDestino()->getZona()->getId(),'peso'=> $envio->getPesoReal()]);
        $envio->setTotalPesoCobrar($envio->getPesoReal());
        $envio->setTotalACobrar($tarifa[0]['total']);

        
        $costo = array('costo' => $tarifa[0]['total']);
        $entityManager = $doctrine->getManager();

        $entityManager->persist($envio);

            // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

         


        return $this->json($costo);
    }


    #[Route('/table', name: 'app_envio_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, EnvioRepository $envioRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        

        $data_table  = $envioRepository->findByDataTable(['page' => $start, 'pageSize' => $length, 'search' => $search['value']]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }

    #[Route('/new', name: 'app_envio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $envio = new Envio();
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($envio);
            $entityManager->flush();

            return $this->redirectToRoute('app_envio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/new.html.twig', [
            'envio' => $envio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_envio_show', methods: ['GET'])]
    public function show(Envio $envio): Response
    {
        return $this->render('envio/show.html.twig', [
            'envio' => $envio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_envio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Envio $envio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnvioType::class, $envio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_envio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('envio/edit.html.twig', [
            'envio' => $envio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_envio_delete', methods: ['POST'])]
    public function delete(Request $request, Envio $envio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$envio->getId(), $request->request->get('_token'))) {
            $entityManager->remove($envio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_envio_index', [], Response::HTTP_SEE_OTHER);
    }
}
