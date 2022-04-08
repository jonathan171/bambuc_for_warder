<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuxiliarController extends AbstractController
{
    #[Route('/auxiliar', name: 'app_auxiliar')]
    public function index(): Response
    {
        return $this->render('auxiliar/index.html.twig', [
            'controller_name' => 'AuxiliarController',
        ]);
    }
}
