<?php

namespace App\Controller;

use App\Entity\FacturaResolucion;
use App\Form\FacturaResolucionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/factura/resolucion')]
class FacturaResolucionController extends AbstractController
{
    #[Route('/', name: 'app_factura_resolucion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $facturaResolucions = $entityManager
            ->getRepository(FacturaResolucion::class)
            ->findAll();

        return $this->render('factura_resolucion/index.html.twig', [
            'factura_resolucions' => $facturaResolucions,
        ]);
    }

    #[Route('/new', name: 'app_factura_resolucion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $facturaResolucion = new FacturaResolucion();
        $form = $this->createForm(FacturaResolucionType::class, $facturaResolucion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($facturaResolucion);
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_resolucion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('factura_resolucion/new.html.twig', [
            'factura_resolucion' => $facturaResolucion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_factura_resolucion_show', methods: ['GET'])]
    public function show(FacturaResolucion $facturaResolucion): Response
    {
        return $this->render('factura_resolucion/show.html.twig', [
            'factura_resolucion' => $facturaResolucion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_factura_resolucion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FacturaResolucion $facturaResolucion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FacturaResolucionType::class, $facturaResolucion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_resolucion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('factura_resolucion/edit.html.twig', [
            'factura_resolucion' => $facturaResolucion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_factura_resolucion_delete', methods: ['POST'])]
    public function delete(Request $request, FacturaResolucion $facturaResolucion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facturaResolucion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($facturaResolucion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_resolucion_index', [], Response::HTTP_SEE_OTHER);
    }
}
