<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdmindController extends AbstractController
{
    #[Route('/admind', name: 'app_admind')]
    public function index(): Response
    {
        return $this->render('admind/index.html.twig', [
            'controller_name' => 'AdmindController',
        ]);
    }
}
