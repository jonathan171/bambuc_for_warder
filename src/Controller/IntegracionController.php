<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Envio;
use App\Entity\Pais;
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
        $headers = array('Accept-Language'=>'eng','Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
        'Accept' => 'application/json');
        $res = $client->request('GET', 'https://express.api.dhl.com/mydhlapi/shipments/'.$codigo_barras.'/tracking?trackingView=all-checkpoints&levelOfDetail=all', ['headers' => $headers]);
        
        //para pruebas 
       /* $headers = array('Accept-Language'=>'eng','Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
        'Accept' => 'application/json');
        $res = $client->request('GET', 'https://express.api.dhl.com/mydhlapi/test/shipments/'.$codigo_barras.'/tracking?trackingView=all-checkpoints&levelOfDetail=all', ['headers' => $headers]);
        */

        // 'application/json; charset=utf8'
        $respuestaServer= json_decode($res->getBody(), true);
        if(array_key_exists('shipments', $respuestaServer)){
            $array_envio = $respuestaServer['shipments'][0];

         
            $envio = $doctrine->getRepository(Envio::class)->findOneBy(['numeroEnvio'=> $array_envio['shipmentTrackingNumber'],'empresa'=> 'DHL']);

            if(!$envio){
                $envio = new Envio();
            }
            $envio->setCodigo($array_envio['productCode']);
            $envio->setEstado(1);
            $envio->setNumeroEnvio($array_envio['shipmentTrackingNumber']); 
            $envio->setDescripcion($array_envio['description']);

            //calcular que se cobrara
            $total_dimension_real = 0;
            $total_peso_real = 0;
            $total_dimension = 0;
            $total_peso = 0;
            foreach($array_envio['pieces'] as $pieza){
                $dimension = (($pieza['dimensions']['length']*$pieza['dimensions']['width']*$pieza['dimensions']['height'])/5000);
                $total_dimension+= $dimension;
                $total_peso+= $pieza['weight'];
                //pesos reales de la transportadora
                $total_peso_real+= $pieza['actualWeight'];
                $dimension_real = (($pieza['actualDimensions']['length']*$pieza['actualDimensions']['width']*$pieza['actualDimensions']['height'])/5000);
                $total_dimension_real+= $dimension_real;

            }
            if($total_dimension>$total_peso){
                $envio->setTotalPesoCobrar(ceil( $total_dimension));
                $envio->setPesoEstimado(ceil( $total_dimension));
            }else{
                $envio->setTotalPesoCobrar(ceil( $total_peso));
                $envio->setPesoEstimado(ceil( $total_peso));
            }
            
            if($total_dimension_real>$total_peso_real){
                $envio->setPesoReal(ceil( $total_dimension_real));
            }else{
                $envio->setPesoReal(ceil( $total_peso_real));
            }
            
            $envio->setTotalACobrar(0);
            
            if(array_key_exists('estimatedDeliveryDate', $array_envio)){
                $fecha = new DateTime($array_envio['estimatedDeliveryDate']);
                $envio->setFechaEstimadaEntrega($fecha);
            }else{
                $fecha = new DateTime();
                $envio->setFechaEstimadaEntrega($fecha);
            }
            $envio->setEmpresa('DHL');
            $pais_envio = $doctrine->getRepository(Pais::class)->findOneBy(['code'=> $array_envio['shipperDetails']['postalAddress']['countryCode']]);
            $pais_recibe = $doctrine->getRepository(Pais::class)->findOneBy(['code'=> $array_envio['receiverDetails']['postalAddress']['countryCode']]);
            
            $envio->setPaisOrigen($pais_envio);
            $envio->setPaisDestino($pais_recibe);
            $envio->setQuienEnvia($array_envio['shipperDetails']['name']);
            $envio->setQuienRecibe($array_envio['receiverDetails']['name']);
            $envio->setJsonRecibido($res->getBody());
            $envio->setFacturado(0);
            

            $entityManager = $doctrine->getManager();

            $entityManager->persist($envio);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();


       

        }
       
        return $this->redirect('/envio');
       
    }
}
