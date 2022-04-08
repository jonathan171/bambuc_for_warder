<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupervisorController extends AbstractController
{
    #[Route('/supervisor', name: 'app_supervisor')]
    public function index(): Response
    {
        return $this->render('supervisor/index.html.twig', [
            'controller_name' => 'SupervisorController',
        ]);
    }
}
