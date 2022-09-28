<?php

namespace App\Controller;

use App\Entity\NotaCredito;
use App\Form\NotaCreditoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nota_credito')]
class NotaCreditoController extends AbstractController
{
    #[Route('/', name: 'app_nota_credito_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $notaCreditos = $entityManager
            ->getRepository(NotaCredito::class)
            ->findAll();

        return $this->render('nota_credito/index.html.twig', [
            'nota_creditos' => $notaCreditos,
        ]);
    }

    #[Route('/new', name: 'app_nota_credito_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notaCredito = new NotaCredito();
        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notaCredito);
            $entityManager->flush();

            return $this->redirectToRoute('app_nota_credito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nota_credito/new.html.twig', [
            'nota_credito' => $notaCredito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nota_credito_show', methods: ['GET'])]
    public function show(NotaCredito $notaCredito): Response
    {
        return $this->render('nota_credito/show.html.twig', [
            'nota_credito' => $notaCredito,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nota_credito_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nota_credito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nota_credito/edit.html.twig', [
            'nota_credito' => $notaCredito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nota_credito_delete', methods: ['POST'])]
    public function delete(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notaCredito->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notaCredito);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nota_credito_index', [], Response::HTTP_SEE_OTHER);
    }
}
