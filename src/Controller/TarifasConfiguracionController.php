<?php

namespace App\Controller;

use App\Entity\TarifasConfiguracion;
use App\Form\TarifasConfiguracionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tarifas/configuracion')]
class TarifasConfiguracionController extends AbstractController
{
    #[Route('/', name: 'app_tarifas_configuracion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tarifasConfiguracions = $entityManager
            ->getRepository(TarifasConfiguracion::class)
            ->findAll();

        return $this->render('tarifas_configuracion/index.html.twig', [
            'tarifas_configuracions' => $tarifasConfiguracions,
        ]);
    }

    #[Route('/new', name: 'app_tarifas_configuracion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tarifasConfiguracion = new TarifasConfiguracion();
        $form = $this->createForm(TarifasConfiguracionType::class, $tarifasConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tarifasConfiguracion);
            $entityManager->flush();

            return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas_configuracion/new.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_configuracion_show', methods: ['GET'])]
    public function show(TarifasConfiguracion $tarifasConfiguracion): Response
    {
        return $this->render('tarifas_configuracion/show.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tarifas_configuracion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TarifasConfiguracion $tarifasConfiguracion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TarifasConfiguracionType::class, $tarifasConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas_configuracion/edit.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_configuracion_delete', methods: ['POST'])]
    public function delete(Request $request, TarifasConfiguracion $tarifasConfiguracion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tarifasConfiguracion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tarifasConfiguracion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
