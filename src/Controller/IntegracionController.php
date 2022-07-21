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
use App\Entity\PaisZona;
use App\Entity\Tarifas;
use App\Repository\PaisZonaRepository;
use App\Repository\TarifasRepository;
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
    public function enviodhl(Request $request, ManagerRegistry $doctrine, TarifasRepository $tarifasRepository, PaisZonaRepository $paisZonaRepository): Response
    {   
        $codigo_barras = $request->request->get('codigo_barras');
        $client = new GuzzleHttp\Client();
        $headers = array('Accept-Language'=>'eng','Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
        'Accept' => 'application/json');
       
        try {
            $client = new  GuzzleHttp\Client();
            $res = $client->get('https://express.api.dhl.com/mydhlapi/shipments/'.$codigo_barras.'/tracking?trackingView=all-checkpoints&levelOfDetail=all', [
                'headers' => $headers
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $res = $e->getResponse();
        }
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
            }else {
                $this->addFlash(
                    'notice',
                    'Este envio ya se encuentra registrado en el sistema por favor verificalo en el listado de envios'
                );
                return $this->redirectToRoute('app_integracion_dhl', [], Response::HTTP_SEE_OTHER);
            }
            $envio->setCodigo($array_envio['productCode']);
            
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
                if(array_key_exists('actualWeight', $pieza)){
                    $total_peso_real+= $pieza['actualWeight'];
                }else{
                    $total_peso_real=0;
                }
                if(array_key_exists('actualDimensions', $pieza)){
               
                $dimension_real = (($pieza['actualDimensions']['length']*$pieza['actualDimensions']['width']*$pieza['actualDimensions']['height'])/5000);
                $total_dimension_real+= $dimension_real;
                }else {
                    
                    $total_dimension_real=0;

                }
                

            }
            if($total_dimension>$total_peso){
                
                if( $total_dimension < 10){
                    $envio->setPesoEstimado($total_dimension);
                    if(fmod($total_dimension, 1) != 0.5){
                        $envio->setTotalPesoCobrar($this->roundUp($total_dimension, 0.5));
                    }else{
                        $envio->setTotalPesoCobrar($total_dimension);
                    }
                    
                }else {
                    $envio->setPesoEstimado( $total_dimension);
                    $envio->setTotalPesoCobrar(ceil( $total_dimension));
                }

                
                
            }else{
                

                if( $total_peso < 10){
                    
                    if(fmod($total_peso, 1) != 0.5){
                        $envio->setTotalPesoCobrar($this->roundUp($total_peso, 0.5));
                       
                    }else{
                        $envio->setTotalPesoCobrar($total_peso);
                    }
                    $envio->setPesoEstimado($total_peso);
                }else {
                    $envio->setPesoEstimado($total_peso);
                    $envio->setTotalPesoCobrar(ceil( $total_peso));
                }
               
               
            }
            
            if($total_dimension_real>$total_peso_real){
              
                
                    $envio->setPesoReal( $total_dimension_real);
                
            }else{
               
                    $envio->setPesoReal($total_peso_real);
                
                
            }
            
            $fecha_envio = new DateTime($array_envio['shipmentTimestamp']);
            $envio->setFechaEnvio($fecha_envio);
            
            if(array_key_exists('estimatedDeliveryDate', $array_envio)){
                $fecha = new DateTime($array_envio['estimatedDeliveryDate']);
                $envio->setFechaEstimadaEntrega($fecha);
                $envio->setEstado(1);
            }else{
                $ultimo_evento = end($array_envio['events']);
                
                $envio->setEstado(3);
                $fecha = new DateTime($ultimo_evento['date']);
                $envio->setFechaEstimadaEntrega($fecha);
            }
            $envio->setEmpresa('DHL');
            $envio->setVerificado(0);

            $pais_envio = $doctrine->getRepository(Pais::class)->findOneBy(['code'=> $array_envio['shipperDetails']['postalAddress']['countryCode']]);
            $pais_recibe = $doctrine->getRepository(Pais::class)->findOneBy(['code'=> $array_envio['receiverDetails']['postalAddress']['countryCode']]);

            

           
            if($array_envio['shipperDetails']['postalAddress']['countryCode']=='CO'){

                $zona =$paisZonaRepository->findOneByZona(['pais'=>$pais_recibe->getId(),'tipo'=> 'exportacion']);
                $tarifa = $tarifasRepository->findOneByPeso(['zona'=>$zona->getZona()->getId(),'peso'=> $envio->getTotalPesoCobrar()]);

            }else {

                $zona =$paisZonaRepository->findOneByZona(['pais'=>$pais_envio->getId(),'tipo'=> 'importacion']);
                $tarifa = $tarifasRepository->findOneByPeso(['zona'=>$zona->getZona()->getId(),'peso'=> $envio->getTotalPesoCobrar()]);
                
            }
            
            if(count( $tarifa )){
                $envio->setTotalACobrar($tarifa[0]['total']);
            }else{
                $envio->setTotalACobrar(0);
            }
            
           
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

            $this->addFlash(
                'notice',
                'Envío Guardado Correctamente'
            );
            return $this->redirect('/envio/'.$envio->getId().'/edit');
         

        }
       
        $this->addFlash(
            'notice',
            'No se encontro ningun envío con este numero de guia porfavor verifica los datos '
        );
        return $this->redirectToRoute('app_integracion_dhl', [], Response::HTTP_SEE_OTHER);
       
    }
    public function roundUp($number, $nearest){
        return $number + ($nearest - fmod($number, $nearest));
    }
}
