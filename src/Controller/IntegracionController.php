<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/integracion')]
class IntegracionController extends AbstractController
{
    #[Route('/', name: 'app_integracion_index')]
    public function index(): Response
    {
        return $this->render('integracion/index.html.twig', [
            'controller_name' => 'IntegracionController',
        ]);
    }

    #[Route('/dhl', name: 'app_integracion_dhl', methods: ['GET', 'POST'])]
    public function dhl(Request $request, EntityManagerInterface $entityManager): Response
    {

        return $this->render('integracion/dhl.html.twig', [
            'controller_name' => 'IntegracionController',
        ]);
    }

    #[Route('/envio_dhl', name: 'app_integracion_enviodhl', methods: ['GET', 'POST'])]
    public function enviodhl(Request $request, EntityManagerInterface $entityManager): Response
    {

        echo 'hola';
        echo  $request->request->get('codigo_barras');
        die();
    }
}
