<?php

namespace App\Command;

use App\Repository\EnvioRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use GuzzleHttp;

class ActualizarEnviosCommand extends Command
{
    private $envioRepository;
    private $doctrine;

    protected static $defaultName = 'app:envio:update';

    public function __construct(EnvioRepository $envioRepository, ManagerRegistry $doctrine)
    {
        $this->envioRepository = $envioRepository;
        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Deletes rejected and spam comments from the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = new GuzzleHttp\Client();
        $headers = array(
            'Accept-Language' => 'eng', 'Authorization' => 'Basic YXBZNWlEMGZRNGpDNXQ6TSQ1elokMmZOQDNvTiM2aA==',
            'Accept' => 'application/json'
        );



        $envios = $this->envioRepository->findSinEntregar();
        foreach ($envios as $envio) {

            try {
                $client = new  GuzzleHttp\Client();
                $res = $client->get('https://express.api.dhl.com/mydhlapi/shipments/' . $envio->getNumeroEnvio() . '/tracking?trackingView=all-checkpoints&levelOfDetail=all', [
                    'headers' => $headers
                ]);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $res = $e->getResponse();
            }

            $respuestaServer= json_decode($res->getBody(), true);
            if(array_key_exists('shipments', $respuestaServer)){
                $array_envio = $respuestaServer['shipments'][0];
    
             
               
    
                //calcular que se cobrara
                $total_dimension_real = 0;
                $total_peso_real = 0;
              
                foreach($array_envio['pieces'] as $pieza){
                   
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
                
                
                if($total_dimension_real>$total_peso_real){
                    $envio->setPesoReal( $total_dimension_real);
                }else{
                    $envio->setPesoReal($total_peso_real);
                }
                
                
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

                $envio->setJsonRecibido($res->getBody());

                
    
                $entityManager = $this->doctrine->getManager();
    
                $entityManager->persist($envio);
    
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
    
    
            }
        }

        $io->success(sprintf('Update "%d"  Envios', count($envios)));

        return 0;
    }
}
