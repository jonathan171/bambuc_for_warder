<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/', name: 'app_inicio')]
    public function inicio(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

         if($this->getUser()){

           
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('app_admind');  
            }
            if($this->isGranted('ROLE_AUXILIAR')){
                return $this->redirectToRoute('app_auxiliar');  
            }
            if($this->isGranted('ROLE_SUPERVISOR')){
                return $this->redirectToRoute('app_supervisor');  
            }
            if($this->isGranted('ROLE_NACIONAL')){
                return $this->redirectToRoute('app_admind');  
            }
            if($this->isGranted('ROLE_AUXILIAR_NACIONAL')){
                return $this->redirectToRoute('app_admind');  
            }
            if($this->isGranted('ROLE_CONTADOR')){
                return $this->redirectToRoute('app_admind');  
            }
         }

        return $this->render('login/landing.html.twig', [
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError();
         // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

         if($this->getUser()){

           
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('app_admind');  
            }
            if($this->isGranted('ROLE_AUXILIAR')){
                return $this->redirectToRoute('app_auxiliar');  
            }
            if($this->isGranted('ROLE_SUPERVISOR')){
                return $this->redirectToRoute('app_supervisor');  
            }
            if($this->isGranted('ROLE_NACIONAL')){
                return $this->redirectToRoute('app_admind');  
            }

            if($this->isGranted('ROLE_AUXILIAR_NACIONAL')){
                return $this->redirectToRoute('app_admind');  
            }
            if($this->isGranted('ROLE_CONTADOR')){
                return $this->redirectToRoute('app_admind');  
            }
         }

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
             'error'         => $error,
        ]);
    }
}
