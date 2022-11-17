<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\NotaCredito;
use App\Entity\NotaCreditoItems;
use App\Form\NotaCreditoType;
use App\Service\EnviarCorreo;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

#[Route('/nota_credito')]
class NotaCreditoController extends AbstractController
{
    #[Route('/', name: 'app_nota_credito_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notaCreditos = $entityManager
            ->getRepository(NotaCredito::class)->createQueryBuilder('nc')
            ->andWhere('nc.facturaCliente = :val')
            ->setParameter('val', $request->request->get('factura_id'))
            ->getQuery()->getResult();

        
        return $this->render('nota_credito/index.html.twig', [
            'nota_creditos' => $notaCreditos,
            'factura_id' => $request->request->get('factura_id')
        ]);
    }

    #[Route('/new', name: 'app_nota_credito_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notaCredito = new NotaCredito();
        $numeroQuery = $entityManager->getRepository(NotaCredito::class)->createQueryBuilder('nc')
                ->orderBy('nc.numeroNota', 'DESC')
                ->setMaxResults(1);
        
        $consulta = $numeroQuery->getQuery()->getOneOrNullResult();
        if($consulta){
            $notaCredito->setNumeroNota($consulta->getNumeroNota()+1);
        }else{
            $notaCredito->setNumeroNota(1);
        }

        $hora = new DateTime();
        $notaCredito->setHora($hora);

        $factura = $entityManager
                    ->getRepository(Factura::class)->find($request->query->get('id'));
        $notaCredito->setFacturaCliente($factura);

        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
        $consulta = $numeroQuery->getQuery()->getOneOrNullResult();
        if($consulta){
            $notaCredito->setNumeroNota($consulta->getNumeroNota()+1);
        }else{
            $notaCredito->setNumeroNota(1);
        }
            $entityManager->persist($notaCredito);
            $entityManager->flush();

            $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $notaCredito->getFacturaCliente()->getId())
            ->getQuery()->getResult();

            foreach($items as $itemsFactura){
                $item = new NotaCreditoItems();
                $item->setCantidad($itemsFactura->getCantidad());
                $item->setDescripcion($itemsFactura->getDescripcion());
                $item->setValorUnitario($itemsFactura->getValorUnitario());
                $item->setSubtotal($itemsFactura->getSubtotal());
                $item->setIva($itemsFactura->getIva());
                $item->setValorIva($itemsFactura->getValorIva());
                $item->setTotal($itemsFactura->getTotal());
                $item->setNotaCredito($notaCredito);
                $item->setCodigo($itemsFactura->getCodigo());
                $item->setRetencionFuente($itemsFactura->getRetencionFuente());
                $item->setValorRetencionFuente($itemsFactura->getValorRetencionFuente());
                $item->setTasaDescuento($itemsFactura->getTasaDescuento());
                $item->setValorDescuento($itemsFactura->getValorDescuento());
                $entityManager->persist($item);
                $entityManager->flush();

            }
            $notaCredito->setSubtotal( $factura->getSubtotal());
            $notaCredito->setTotalIva($factura->getTotalIva());
            $notaCredito->setTotal($factura->getTotal());
            $notaCredito->setReteFuente($factura->getReteFuente());
            $notaCredito->setTotalReteFuenteG($factura->getTotalReteFuenteG());
            $entityManager->persist($notaCredito);
            $entityManager->flush();
            if ($notaCredito->getConceptoCredito() == 2) {
                
                    $this->AnularFactura($factura, $items, $entityManager);
                
               
            }

            return $this->redirectToRoute('app_nota_credito_edit', ['id'=>$notaCredito->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nota_credito/new.html.twig', [
            'nota_credito' => $notaCredito,
            'form' => $form,
            'factura' => $factura
        ]);
    }

    #[Route('/{id}/show', name: 'app_nota_credito_show', methods: ['GET'])]
    public function show(NotaCredito $notaCredito): Response
    {
        return $this->render('nota_credito/show.html.twig', [
            'nota_credito' => $notaCredito,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nota_credito_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotaCreditoType::class, $notaCredito);
        $form->handleRequest($request);
        $factura= $notaCredito->getFacturaCliente();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nota_credito_index', [], Response::HTTP_SEE_OTHER);
        }
        $items = $entityManager->getRepository(NotaCreditoItems::class)->createQueryBuilder('nci')
            ->andWhere('nci.notaCredito = :val')
            ->setParameter('val', $notaCredito->getId())
            ->getQuery()->getResult();

        return $this->renderForm('nota_credito/edit.html.twig', [
            'nota_credito' => $notaCredito,
            'form' => $form,
            'factura' => $factura,
            'items' => $items
        ]);
    }

    #[Route('/{id}/delete', name: 'app_nota_credito_delete', methods: ['POST'])]
    public function delete(Request $request, NotaCredito $notaCredito, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notaCredito->getId(), $request->request->get('_token'))) {
            $entityManager->remove($notaCredito);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nota_credito_index', [], Response::HTTP_SEE_OTHER);
    }
    public function AnularFactura($facturaanulada, $itemsAnulados, $entityManager) {

        
        if (!$facturaanulada->getAnulado()) {
        
            foreach ($itemsAnulados as $itemAnulado) {

               
            $envios = $entityManager->getRepository(Envio::class)->createQueryBuilder('e')
            ->andWhere('e.facturaItems = :val')
            ->setParameter('val', $itemAnulado->getId())
            ->getQuery()->getResult();
               foreach ( $envios as $envio) {
                
                    $envio->setFacturado(0);
                    $envio->setFacturaItems(Null);
                    $entityManager->persist($envio);
                    $entityManager->flush();
               }
              
            }
            $facturaanulada->setAnulado(1);
            $facturaanulada->setEstado(7);
            $fecha = new DateTime();
            $facturaanulada->setAnuladoFecha( $fecha);
            $user = $this->getUser();
            $facturaanulada->setAnuladoUsuario($user->getId());
            $entityManager->persist($facturaanulada);
            $entityManager->flush();
        }
    }

    #[Route('item_delete/{id}', name: 'app_nota_credito_item_delete', methods: ['POST'])]
    public function itemDelete(Request $request, NotaCreditoItems $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {

            $nota = $entityManager->getRepository(NotaCredito::class)->find($item->getNotaCredito()->getId());
            $nota->setSubtotal($nota->getSubtotal() - $item->getSubtotal());
            $nota->setTotal($nota->getTotal() - $item->getTotal());
            $nota->setTotalIva($nota->getTotalIva() - $item->getValorIva());
            $nota->setTotalReteFuenteG(($nota->getReteFuente() / 100) * $nota->getSubtotal());

            $entityManager->persist($nota);
            $entityManager->flush();


            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nota_credito_edit', ['id' => $nota->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reportar', name: 'app_nota_credito_reportar', methods: ['GET', 'POST'])]
    public function executeReportar(
        Request $request,
        EntityManagerInterface $entityManager,
        EnviarCorreo $enviar
    ) {
        $CuerpoJson =  array();
        $nota = $entityManager->getRepository(NotaCredito::class)->find($request->request->get('id'));
        $factura = $nota->getFacturaCliente();

        $consectoCredito = array(
            '1' => 'DEVOLUCION',
            '2' => 'ANULACION',
            '3' => 'REBAJA',
            '4' => 'DESCUENTO',
            '5' => 'RECISION',
            '6' => 'OTROS'
        );

        $consectoDebito = array(
            '1' => 'INTERESES',
            '2' => 'GASTOS',
            '3' => 'CAMBIO_VALOR',
            '4' => 'OTROS'
        );

        if ($request->request->get('tipo') == 91) {
            $x_nota = 'credit_note';
            $prefijo = "NCE";
        } elseif ($request->request->get('tipo') == 92) {
        
            $x_nota = 'debit_note';
            $prefijo = "NDE";
        }

        $CuerpoJson['actions']['send_dian'] = true;
        $CuerpoJson['actions']['send_email'] = false;

        $CuerpoJson[$x_nota]['issue_date'] = $nota->getFecha()->format('d/m/Y');
        if ($request->request->get('tipo') == 91) {
            $CuerpoJson[$x_nota]['reason'] = $consectoCredito[$nota->getConceptoCredito()];
        } elseif ($request->request->get('tipo') == 92) {
            $CuerpoJson[$x_nota]['reason'] = $consectoDebito[$nota->getConceptoDebito()];
        }
        if ($factura->getCufe() != null && $factura->getCufe() != '') {
            $respues = json_decode($factura->getRespuestaDian(), true); 
        } else {
            return $this->json(array(
                        "xml" => 'fallo'
            ));
        }
        $CuerpoJson[$x_nota]['invoice_id'] = $respues['uuid'];
        $CuerpoJson[$x_nota]['number'] = $nota->getNumeroNota();
        $CuerpoJson[$x_nota]['numbering']['prefix'] = $prefijo;

        $items = $entityManager->getRepository(NotaCreditoItems::class)->createQueryBuilder('nci')
            ->andWhere('nci.notaCredito = :val')
            ->setParameter('val', $nota->getId())
            ->getQuery()->getResult();
       
            foreach ($items as $item) {
                $itemJ ["sku"] = $item->getCodigo();
                $itemJ ["quantity"] = (int)$item->getCantidad();
                $itemJ ["description"] = $item->getDescripcion();
                 $itemJ ["price"]=(double)$item->getValorUnitario();
        
    
                if($item->getTasaDescuento()){
                    
                   $itemJ["discount_rate"] = $item->getTasaDescuento();  
                }
                
                
                
                $itemJ ["taxes"] =array();
                $itemJ ["retentions"] = array(
                );
                if ($item->getIva() > 0) {
                    array_push($itemJ['taxes'], array(
                        "tax_category" => "IVA",
                        "tax_rate" => (int) $item->getIva()));
                }
                if ($item->getRetencionFuente() > 0) {
                    array_push($itemJ['retentions'], array(
                        "tax_category" => "RET_FUENTE",
                        "tax_rate" => (int) $item->getRetencionFuente()));
                }
                array_push($CuerpoJson[$x_nota]['items'], $itemJ);
            }
            $CuerpoJson[$x_nota]['dataico_account_id'] = '01814067-cc44-808a-83a1-de850ba1e360';
            $CuerpoJson[$x_nota]['env'] = 'PRODUCCION';
        

        $this->llamarNotaDataico($CuerpoJson, $nota, $entityManager);

        



        $responseData = array(
            "results" => 'success',
        );
        return $this->json($responseData);
    }

    public function llamarNotaDataico($Json,NotaCredito $nota,  EntityManagerInterface $entityManager)
    {
        //lamada a identificarse
        $prueba = 0;

       
        $headers = array('Content-Type' => 'application/json', 'auth-token' => '232828f7e45e42e74ac28a0e0dbe4053');


        $nota->setCuerpoJsonf(json_encode($Json));


        if ($nota->getTipo() == 'credito') {
            try {
                $client = new  GuzzleHttp\Client();
                $guzzleResult = $client->post('https://api.dataico.com/direct/dataico_api/v2/credit_notes', [
                    'headers' => $headers,
                    'body' => json_encode($Json)
                ]);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $guzzleResult = $e->getResponse();
            }
        } else {
            try {
                $client = new  GuzzleHttp\Client();
                $guzzleResult = $client->post('https://api.dataico.com/direct/dataico_api/v2/debit_notes', [
                    'headers' => $headers,
                    'body' => json_encode($Json)
                ]);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $guzzleResult = $e->getResponse();
            }
        }
       


        $respuesta = $guzzleResult->getBody()->getContents();
        $nota->setRespuestaDian($respuesta);

        $respuestaServer =  json_decode($respuesta, true);




        $entityManager->persist($nota);
        $entityManager->flush();



        if (array_key_exists('dian_status', $respuestaServer)) {
            if ($respuestaServer['dian_status'] == 'DIAN_ACEPTADO' || $prueba == 1) {
                if (array_key_exists('cufe', $respuestaServer)) {
                    $nota->setCufe($respuestaServer['cufe']);
                    $nota->setRespuestaCorreo($respuestaServer['cufe']);
              
                }
                $entityManager->persist($nota);
                $entityManager->flush();

                if ($respuestaServer['qrcode'] != null && $respuestaServer['qrcode'] != '') {
                    $codesDir = "uploads/assets/codes/";
                    $codeFile = 'N'. $nota->getId() . '.png';
                    $result = Builder::create()
                        ->writer(new PngWriter())
                        ->writerOptions([])
                        ->data($respuestaServer['qrcode'])
                        ->encoding(new Encoding('UTF-8'))
                        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                        ->size(250)
                        ->margin(5)
                        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                        ->labelText('')
                        ->labelFont(new NotoSans(20))
                        ->labelAlignment(new LabelAlignmentCenter())
                        ->build();
                    $result->saveToFile($codesDir . $codeFile);

                   
                }

                
            }
        }

        
    }
}
