<?php

namespace App\Controller;


use App\Entity\NotaCredito;
use App\Form\NotaCreditoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/nota_debito')]
class NotaDebitoController extends AbstractController
{
    #[Route('/', name: 'app_nota_debito_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $notaCreditos = $entityManager
            ->getRepository(NotaCredito::class)
            ->findAll();

        return $this->render('nota_debito/index.html.twig', [
            'nota_debitos' => $notaCreditos,
        ]);
    }

    #[Route('/new', name: 'app_nota_debito_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notaCredito = new NotaCredito();
        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notaCredito);
            $entityManager->flush();

            return $this->redirectToRoute('app_nota_debito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nota_debito/new.html.twig', [
            'nota_debito' => $notaCredito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nota_debito_show', methods: ['GET'])]
    public function show(NotaCredito $notaCredito): Response
    {
        return $this->render('nota_debito/show.html.twig', [
            'nota_debito' => $notaCredito,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nota_debito_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nota_debito_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nota_debito/edit.html.twig', [
            'nota_debito' => $notaCredito,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nota_debito_delete', methods: ['POST'])]
    public function delete(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notaCredito->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notaCredito);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nota_debito_index', [], Response::HTTP_SEE_OTHER);
    }
}
