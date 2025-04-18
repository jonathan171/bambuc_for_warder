<?php

namespace App\Controller;

use App\Entity\Pais;
use App\Form\PaisType;
use App\Repository\PaisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pais')]
class PaisController extends AbstractController
{
    #[Route('/', name: 'app_pais_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    { 
        return $this->render('pais/index.html.twig', [
        ]);
    }

    #[Route('/table', name: 'app_pais_table', methods: ['GET', 'POST'])]
    public function table(Request $request, PaisRepository $paisRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        

        $data_table  = $paisRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }

    #[Route('/new', name: 'app_pais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pai = new Pais();
        $form = $this->createForm(PaisType::class, $pai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pai);
            $entityManager->flush();

            return $this->redirectToRoute('app_pais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pais/new.html.twig', [
            'pai' => $pai,
            'form' => $form,
        ]);
    }
    #[Route('/buscadorAjaxPais', name: 'app_pais_buscador_ajax_pais', methods: ['GET', 'POST'])]
    public function executeBuscadorAjaxCliente(
        Request $request,
        PaisRepository $paisRepository
    ) {
        $busqueda = $request->query->get('term');



        $start = 0;
        $length = 20;


        $data_table  = $paisRepository->findByDataShearch([
            'page' => ($start / $length),
            'pageSize' =>  $length,
            'search' =>  $busqueda
        ]);


        $responseData = array(
            "results" => $data_table['data'],
            "pagination" => array(
                // Determinar si hay mas paginas disponibles
                "more" => (false)
            )
        );
        return $this->json($responseData);
    }

    #[Route('/{id}', name: 'app_pais_show', methods: ['GET'])]
    public function show(Pais $pai): Response
    {
        return $this->render('pais/show.html.twig', [
            'pai' => $pai,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pais $pai, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaisType::class, $pai);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pais/edit.html.twig', [
            'pai' => $pai,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pais_delete', methods: ['POST'])]
    public function delete(Request $request, Pais $pai, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pai->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pai);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pais_index', [], Response::HTTP_SEE_OTHER);
    }
}
