<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Form\EnvioType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EnvioRepository;
use App\Repository\TarifasRepository;
use App\Entity\TarifasConfiguracion;
use App\Repository\PaisRepository;
use App\Repository\PaisZonaRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    #[Route('/actualizarvalor', name: 'app_envio_actualizarvalor', methods: ['GET', 'POST'])]
    public function actualizarvalor(Request $request, EntityManagerInterface $entityManager, TarifasRepository $tarifasRepository,  PaisZonaRepository $paisZonaRepository): Response
    {
        
         $envio= $entityManager
            ->getRepository(Envio::class)
            ->find($request->request->get('id'));

            

            

           
            if($envio->getPaisOrigen()->getCode()=='CO' ){

                $zona =$paisZonaRepository->findOneByZona(['pais'=>$envio->getPaisDestino()->getId(),'tipo'=> 'exportacion']);
                $tarifa = $tarifasRepository->findOneByPeso(['zona'=>$zona->getZona()->getId(),'peso'=> $envio->getPesoReal()]);

            }else {

                $zona =$paisZonaRepository->findOneByZona(['pais'=>$envio->getPaisOrigen()->getId(),'tipo'=> 'importacion']);
                $tarifa = $tarifasRepository->findOneByPeso(['zona'=>$zona->getZona()->getId(),'peso'=> $envio->getPesoReal()]);
                
            }
        
        $envio->setTotalPesoCobrar($envio->getPesoReal());
        $envio->setTotalACobrar($tarifa[0]['total']);

      
       
       
            $entityManager->persist($envio);
            $entityManager->flush();
            
            $costo = array('costo' => $envio->getTotalACobrar()); 


        return $this->json($costo);
    }


    #[Route('/table', name: 'app_envio_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, EnvioRepository $envioRepository): Response
    {
        $search =  $request->request->get('search');
        $start = $request->request->get('start');
        $length = $request->request->get('length');

        

        $data_table  = $envioRepository->findByDataTable(['page' => ($start/$length), 'pageSize' => $length, 'search' => $search['value']]);

        // Objeto requerido por Datatables

        $responseData = array(
            "draw" => '',
            "recordsTotal" => $data_table['totalRecords'],
            "recordsFiltered" => $data_table['totalRecords'],
            "data" => $data_table['data']
        );


        return $this->json($responseData);
    }
    //listado de envios sin facturar

    #[Route('/listado_envios', name: 'app_envio_listado_envios', methods: ['GET','POST'])]
    public function listadoEnvios(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $query = $entityManager->getRepository(Envio::class)->createQueryBuilder('e');
        
        if($request->request->get('fecha_inicio')){

            $query->andWhere('e.fechaEnvio >= :val')
            ->setParameter('val',$request->request->get('fecha_inicio'))
            ->andWhere('e.fechaEnvio <= :val1')
            ->setParameter('val1',$request->request->get('fecha_fin'));
        }
         $envios = $query->andWhere('e.facturado = 0')
                        ->getQuery()->getResult();

         

         return $this->render('envio/listado_envios.html.twig', [
            'envios'     => $envios,
            'factura_id' => $request->request->get('factura_id')
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

    #[Route('/{id}/mostrar', name: 'app_envio_show', methods: ['GET'])]
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
