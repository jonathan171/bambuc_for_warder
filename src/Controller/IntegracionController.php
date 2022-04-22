<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Envio;
use DateTime;
use GuzzleHttp;

#[Route('/integracion')]
class IntegracionController extends AbstractController
{
    #[Route('/', name: 'app_integracion_index')]
    public function index(Request $request): Response
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
    public function enviodhl(Request $request, ManagerRegistry $doctrine): Response
    {   
        $codigo_barras = $request->request->get('codigo_barras');
        $client = new GuzzleHttp\Client();
        /*$headers = array('Accept-Language'=>'eng','Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
        'Accept' => 'application/json');
        $res = $client->request('GET', 'https://express.api.dhl.com/mydhlapi/shipments/'.$codigo_barras.'/tracking?trackingView=all-checkpoints&levelOfDetail=all', ['headers' => $headers]);
        */
        //para pruebas 
        $headers = array('Accept-Language'=>'eng','Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
        'Accept' => 'application/json');
        $res = $client->request('GET', 'https://express.api.dhl.com/mydhlapi/test/shipments/'.$codigo_barras.'/tracking?trackingView=all-checkpoints&levelOfDetail=all', ['headers' => $headers]);
        

        // 'application/json; charset=utf8'
        $respuestaServer= json_decode($res->getBody(), true);
        if(array_key_exists('shipments', $respuestaServer)){
            $array_envio = $respuestaServer['shipments'][0];

         
            $envio = $doctrine->getRepository(Envio::class)->findOneBy(['codigo'=> $array_envio['shipmentTrackingNumber'],'empresa'=> 'DHL']);

         //   if(!$envio){
                $envio = new Envio();
           // }
            $envio->setCodigo($array_envio['shipmentTrackingNumber']);
            $envio->setEstado(1);
            $envio->setNumeroOrden($array_envio['shipmentTrackingNumber']);
            $envio->setDescripcion($array_envio['description']);
            $envio->setTotalPesoEstimado($array_envio['totalWeight']);
            if(array_key_exists('estimatedDeliveryDate', $respuestaServer)){

                $envio->setFechaEstimadaEntrega($array_envio['estimatedDeliveryDate']);
            }else{
                $fecha = new DateTime();
                $envio->setFechaEstimadaEntrega($fecha);
            }
//$envio->setFechaEstimadaEntrega($array_envio['estimatedDeliveryDate']);
            $envio->setJsonRecibido($res->getBody());
            $envio->setFacturado(0);
            $envio->setCantidadPiezas($array_envio['numberOfPieces']);
            $envio->setPiezas(json_encode( $array_envio['pieces'] ));
            $envio->setEmpresa('DHL');

            $entityManager = $doctrine->getManager();

            $entityManager->persist($envio);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();


       

        }
       
        return $this->redirect('/envio');
       
    }
}
