<?php

namespace App\Controller;

use App\Entity\Zonas;
use App\Form\ZonasType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/zonas')]
class ZonasController extends AbstractController
{
    #[Route('/', name: 'app_zonas_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $zonas = $entityManager
            ->getRepository(Zonas::class)
            ->findAll();

        return $this->render('zonas/index.html.twig', [
            'zonas' => $zonas,
        ]);
    }

    #[Route('/new', name: 'app_zonas_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $zona = new Zonas();
        $form = $this->createForm(ZonasType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($zona);
            $entityManager->flush();

            return $this->redirectToRoute('app_zonas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('zonas/new.html.twig', [
            'zona' => $zona,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_zonas_show', methods: ['GET'])]
    public function show(Zonas $zona): Response
    {
        return $this->render('zonas/show.html.twig', [
            'zona' => $zona,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_zonas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Zonas $zona, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ZonasType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_zonas_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('zonas/edit.html.twig', [
            'zona' => $zona,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_zonas_delete', methods: ['POST'])]
    public function delete(Request $request, Zonas $zona, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$zona->getId(), $request->request->get('_token'))) {
            $entityManager->remove($zona);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_zonas_index', [], Response::HTTP_SEE_OTHER);
    }
}
