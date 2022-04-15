<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Form\EnvioType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
