<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\Municipio;
use App\Entity\Pais;
use App\Entity\PaisZona;
use App\Form\ClientesType;
use App\Repository\ClientesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clientes')]
class ClientesController extends AbstractController
{
    #[Route('/', name: 'app_clientes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $clientes = $entityManager
            ->getRepository(Clientes::class)
            ->findAll();

        return $this->render('clientes/index.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/table', name: 'app_clientes_table', methods: ['GET', 'POST'])]
    public function table(Request $request, ClientesRepository $clienteRepository): Response
    {
       
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        

        $data_table  = $clienteRepository->findByDataTable([
                             'page' => ($start /$length),
                             'pageSize' =>  $length,
                             'search' =>  $search["value"]
                            ]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }

    #[Route('/actualizar_zona', name: 'app_clientes_actualizar_zona', methods: ['GET', 'POST'])]
    public function actualizarZona(Request $request, EntityManagerInterface $entityManagers, ManagerRegistry $doctrine): Response
    {
        $paises= $entityManagers
        ->getRepository(Pais::class)
        ->findAll();
        $entityManager = $doctrine->getManager();
        foreach($paises as $pais){
            $paisZona = new PaisZona ();

            $paisZona->setPais($pais);
            $paisZona->setZona($pais->getZona());
           

            $entityManager->persist($paisZona);
            $entityManager->flush();

        }
        
        $responseData = array(
            "data" => 'terminado'
        );


        return $this->json($responseData);
    }



    #[Route('/new', name: 'app_clientes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cliente = new Clientes();

        $municipio = $entityManager->getRepository(Municipio::class)->find(2);
       
        $cliente ->setMunicipio($municipio);

        $form = $this->createForm(ClientesType::class, $cliente);
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cliente);
            $entityManager->flush();

            return $this->redirectToRoute('app_clientes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clientes/new.html.twig', [
            'cliente' => $cliente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clientes_show', methods: ['GET'])]
    public function show(Clientes $cliente): Response
    {
        return $this->render('clientes/show.html.twig', [
            'cliente' => $cliente,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clientes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clientes $cliente, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(ClientesType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_clientes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clientes/edit.html.twig', [
            'cliente' => $cliente,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clientes_delete', methods: ['POST'])]
    public function delete(Request $request, Clientes $cliente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cliente->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cliente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_clientes_index', [], Response::HTTP_SEE_OTHER);
    }
}
