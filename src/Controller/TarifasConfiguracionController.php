<?php

namespace App\Controller;

use App\Entity\TarifasConfiguracion;
use App\Entity\Tarifas;
use App\Form\TarifasConfiguracionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tarifas_configuracion')]
class TarifasConfiguracionController extends AbstractController
{
    #[Route('/', name: 'app_tarifas_configuracion_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $tarifasConfiguracions = $entityManager
            ->getRepository(TarifasConfiguracion::class)
            ->findAll();

        return $this->render('tarifas_configuracion/index.html.twig', [
            'tarifas_configuracions' => $tarifasConfiguracions,
        ]);
    }

    #[Route('/new', name: 'app_tarifas_configuracion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $tarifasConfiguracion = new TarifasConfiguracion();
        $form = $this->createForm(TarifasConfiguracionType::class, $tarifasConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tarifasConfiguracion);
            $entityManager->flush();

            return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas_configuracion/new.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_configuracion_show', methods: ['GET'])]
    public function show(TarifasConfiguracion $tarifasConfiguracion): Response
    {
        return $this->render('tarifas_configuracion/show.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tarifas_configuracion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TarifasConfiguracion $tarifasConfiguracion, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(TarifasConfiguracionType::class, $tarifasConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $tarifas = $entityManager->getRepository(Tarifas::class)->createQueryBuilder('t')
                ->andWhere('t.tarifasConfiguracion = :val')
                ->setParameter('val', $tarifasConfiguracion->getId())
                ->getQuery()->getResult();

            $variables = $entityManager
                ->getRepository(TarifasConfiguracion::class)
                ->find($tarifasConfiguracion->getId());

            if ($variables->getTipo() == 'exportacion') {

                foreach ($tarifas as $tarifa) {

                    $total = (($tarifa->getCostoFlete() + (($variables->getTasaConbustible() / 100) * $tarifa->getCostoFlete())) * $variables->getValorDolar()) / ((100 - $variables->getPorcentajeGanacia()) / 100);
                    $tarifa->setTotal(round($total, -3));
                    $entityManager->persist($tarifa);
                    $entityManager->flush();
                }
            }elseif($variables->getTipo() == 'importacion'){

                foreach ($tarifas as $tarifa) {

                    $total = (($tarifa->getCostoFlete()+$tarifa->getCostoFlete()*($variables->getTasaConbustible() / 100)+$tarifa->getZona()->getSituacionEmergencia()*$tarifa->getPesoMinimo())* $variables->getValorDolar())/(1-($variables->getPorcentajeGanacia()/100));
                    $tarifa->setTotal(round($total, -3));
                    $entityManager->persist($tarifa);
                    $entityManager->flush();
                }

            }elseif($variables->getTipo() == 'especial_importacion'){

                foreach ($tarifas as $tarifa) {

                 

                        $total = ((($tarifa->getCostoFlete()+$tarifa->getCostoFlete()*($variables->getTasaConbustible() / 100))+($tarifa->getZona()->getSituacionEmergencia()*$tarifa->getPesoMinimo()))* $variables->getValorDolar())/(1-($tarifa->getPorcentaje()/100));
                        $tarifa->setTotal(round($total, -3));
                        $entityManager->persist($tarifa);
                        $entityManager->flush();


                    

                }

                }elseif($variables->getTipo() == 'especial_exportacion'){

                    foreach ($tarifas as $tarifa) {

                        if($tarifa->getPesoMinimo()<=14){
    
                            $total = ((($tarifa->getCostoFlete()+$tarifa->getCostoFlete()*($variables->getTasaConbustible() / 100))+($tarifa->getZona()->getSituacionEmergencia()*$tarifa->getPesoMinimo()))* $variables->getValorDolar())/(1-($tarifa->getPorcentaje()/100));
                            $tarifa->setTotal(round($total, -3));
                            $entityManager->persist($tarifa);
                            $entityManager->flush();
    
    
                        }elseif($tarifa->getPesoMinimo()>14 && $tarifa->getPesoMinimo() <=30 ){
                            $total = ($tarifa->getCostoFlete())/(1-($tarifa->getPorcentaje()/100));
                            $tarifa->setTotal(round($total, -3));
                            $entityManager->persist($tarifa);
                            $entityManager->flush();
    
                        }elseif($tarifa->getPesoMinimo()>30 ){
                            $total = ($tarifa->getCostoFlete()*$tarifa->getPesoMinimo())/(1-($tarifa->getPorcentaje()/100));
                            $tarifa->setTotal(round($total, -3));
                            $entityManager->persist($tarifa);
                            $entityManager->flush();
    
                        }
    
                    }

                

            }
            

            return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifas_configuracion/edit.html.twig', [
            'tarifas_configuracion' => $tarifasConfiguracion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tarifas_configuracion_delete', methods: ['POST'])]
    public function delete(Request $request, TarifasConfiguracion $tarifasConfiguracion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tarifasConfiguracion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tarifasConfiguracion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tarifas_configuracion_index', [], Response::HTTP_SEE_OTHER);
    }
}
