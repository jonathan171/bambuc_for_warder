<?php

namespace App\Controller;

use App\Entity\Tarifas;
use App\Form\TarifasType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TarifasRepository;

#[Route('/tarifas')]
class TarifasController extends AbstractController
{
    #[Route('/', name: 'app_tarifas_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tarifas = $entityManager
            ->getRepository(Tarifas::class)
            ->findAll();

        return $this->render('tarifas/index.html.twig', [
            'tarifas' => $tarifas,
        ]);
    }
    #[Route('/variables', name: 'app_tarifas_variables', methods: ['GET', 'POST'])]
    public function variables(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository): Response
    {
        $tarifas = $entityManager
            ->getRepository(Tarifas::class)
            ->find(1);

        
       
      return $this->json($responseData);

    }
    #[Route('/table', name: 'app_tarifas_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');
        
        $campos = array(
            "pesoMinimo",
            "pesoMaximo",
            "costoFlete",
            "zona",
            "total"
            );
        
           $data_table  = $tarifasRepository->findByDataTable(['page'=>$start, 'pageSize'=>$length,'search'=>$search['value']]);
           
            // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" =>$data_table['totalRecords'],
            "data" => $data_table['data']
        );

       
      return $this->json($responseData);

    }

    #[Route('/new', name: 'app_tarifas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tarifa = new Tarifas();
        $form = $this->createForm(TarifasType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tarifa);
            $entityManager->flush();

            return $this->redirectToRoute('app_tarifas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas/new.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_show', methods: ['GET'])]
    public function show(Tarifas $tarifa): Response
    {
        return $this->render('tarifas/show.html.twig', [
            'tarifa' => $tarifa,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tarifas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tarifas $tarifa, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TarifasType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tarifas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas/edit.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_delete', methods: ['POST'])]
    public function delete(Request $request, Tarifas $tarifa, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tarifa->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tarifa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tarifas_index', [], Response::HTTP_SEE_OTHER);
    }

   
}
