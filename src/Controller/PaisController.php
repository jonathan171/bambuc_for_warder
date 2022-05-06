<?php

namespace App\Controller;

use App\Entity\Pais;
use App\Form\PaisType;
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
        $pais = $entityManager
            ->getRepository(Pais::class)
            ->findAll();

        return $this->render('pais/index.html.twig', [
            'pais' => $pais,
        ]);
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
