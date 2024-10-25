<?php

namespace App\Controller;

use App\Entity\Municipio;
use App\Form\MunicipioType;
use App\Repository\MunicipioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/municipio')]
class MunicipioController extends AbstractController
{
    #[Route('/', name: 'app_municipio_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('municipio/index.html.twig', [
        ]);
    }

    #[Route('/table', name: 'app_municipio_table', methods: ['GET', 'POST'])]
    public function table(Request $request, MunicipioRepository $municipioRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        

        $data_table  = $municipioRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }
    #[Route('/new', name: 'app_municipio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $municipio = new Municipio();
        $form = $this->createForm(MunicipioType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($municipio);
            $entityManager->flush();

            return $this->redirectToRoute('app_municipio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('municipio/new.html.twig', [
            'municipio' => $municipio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_municipio_show', methods: ['GET'])]
    public function show(Municipio $municipio): Response
    {
        return $this->render('municipio/show.html.twig', [
            'municipio' => $municipio,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_municipio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Municipio $municipio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MunicipioType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_municipio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('municipio/edit.html.twig', [
            'municipio' => $municipio,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_municipio_delete', methods: ['POST'])]
    public function delete(Request $request, Municipio $municipio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$municipio->getId(), $request->request->get('_token'))) {
            $entityManager->remove($municipio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_municipio_index', [], Response::HTTP_SEE_OTHER);
    }
}
