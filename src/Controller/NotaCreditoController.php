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
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('id'));

        $CuerpoJson['actions']['send_dian'] = true;
        $CuerpoJson['actions']['send_email'] = false;


        $CuerpoJson['invoice']['number'] = $factura->getNumeroFactura();
        $CuerpoJson['invoice']['invoice_type_code'] = 'FACTURA_VENTA';
        $CuerpoJson['invoice']['payment_means_type'] = $factura->getFormaDePago();
        $CuerpoJson['invoice']['payment_means'] = $factura->getCondDePago()->getDescripcioDataico();
        $CuerpoJson['invoice']['issue_date'] = $factura->getFecha()->format('d/m/Y');
        $CuerpoJson['invoice']['payment_date'] = $factura->getFechaVencimiento()->format('d/m/Y');
        $CuerpoJson['invoice']['numbering']['resolution_number'] = $factura->getFacturaResolucion()->getNumeroResolucion();
        $CuerpoJson['invoice']['numbering']['prefix'] = $factura->getFacturaResolucion()->getPrefijo();
        $notes = $factura->getObservaciones();
        if ($notes) {
            $CuerpoJson['invoice']['notes'] = array(0 => $notes);
        }
        $CuerpoJson['invoice']['customer']['address_line'] = $factura->getCliente()->getDireccion();
        $CuerpoJson['invoice']['customer']['city'] = $factura->getCliente()->getMunicipio()->getCodigo();
        $CuerpoJson['invoice']['customer']['company_name'] = $factura->getCliente()->getRazonSocial();
        $CuerpoJson['invoice']['customer']['department'] = $factura->getCliente()->getMunicipio()->getDepartamento()->getCodigo();
        $CuerpoJson['invoice']['customer']['email'] = $factura->getCliente()->getCorreo();
        $CuerpoJson['invoice']['customer']['family_name'] = $factura->getCliente()->getApellidos();
        $CuerpoJson['invoice']['customer']['first_name'] = $factura->getCliente()->getNombres();
        $CuerpoJson['invoice']['customer']['phone'] = $factura->getCliente()->getTelefono();

        if ($factura->getCliente()->getTipoDocumento() == "NIT") {
            $nits = explode("-", $factura->getCliente()->getNit());
            $numero = $nits[0];
            $CuerpoJson['invoice']['customer']['party_identification'] = $numero;
        } else {

            $CuerpoJson['invoice']['customer']['party_identification'] = $factura->getCliente()->getNit();
        }

        $CuerpoJson['invoice']['customer']['party_identification_type'] = $factura->getCliente()->getTipoDocumento();
        $CuerpoJson['invoice']['customer']['party_type'] = $factura->getCliente()->getTipoReceptor();
        $CuerpoJson['invoice']['customer']['regimen'] = $factura->getRegimen();
        $CuerpoJson['invoice']['customer']['tax_level_code'] = $factura->getTaxLevelCode();

        $CuerpoJson['invoice']['items'] = array();

        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        foreach ($items as $item) {
            $itemJ = array(
                "sku" => $item->getCodigo(),
                "quantity" => (int) $item->getCantidad(),
                "description" => $item->getDescripcion(),
                "price" => (float) $item->getValorUnitario()
            );

            if ($item->getTasaDescuento() > 0) {

                $itemJ["discount_rate"] = $item->getTasaDescuento();
            }

            if ($item->getIva() > 0) {
                $itemJ['taxes'] = array();
                array_push($itemJ['taxes'], array(
                    "tax_category" => "IVA",
                    "tax_rate" => (float) $item->getIva()
                ));
            }
            if ($item->getRetencionFuente() > 0) {
                $itemJ['retentions'] = array();
                array_push($itemJ['retentions'], array(
                    "tax_category" => "RET_FUENTE",
                    "tax_rate" => (float) $item->getRetencionFuente()
                ));
            }
            array_push($CuerpoJson['invoice']['items'], $itemJ);
        }

        if ($factura->getTotalIva() > 0) {
            $porIva = ($factura->getTotalReteIva() * 100) / $factura->getTotalIva();
        } else {
            $porIva = 0;
        }

        if ($porIva > 0) {

            if (array_key_exists('retentions', $CuerpoJson['invoice'])) {

                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_IVA",
                    "tax_rate" => $porIva
                ));
            } else {

                $CuerpoJson['invoice']['retentions'] = array();
                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_IVA",
                    "tax_rate" => $porIva
                ));
            }
        }
        $porIca = ($factura->getTotalReteIca() * 100) / $factura->getSubtotal();
        if ($porIca > 0) {


            if (array_key_exists('retentions', $CuerpoJson['invoice'])) {

                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_ICA",
                    "tax_rate" => ($factura->getTotalReteIca() * 100) / $factura->getSubtotal()
                ));
            } else {

                $CuerpoJson['invoice']['retentions'] = array();
                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_ICA",
                    "tax_rate" => ($factura->getTotalReteIca() * 100) / $factura->getSubtotal()
                ));
            }
        }
        if ($factura->getReteFuente() > 0) {

            if (array_key_exists('retentions', $CuerpoJson['invoice'])) {

                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_FUENTE",
                    "tax_rate" => $factura->getReteFuente()
                ));
            } else {

                $CuerpoJson['invoice']['retentions'] = array();
                array_push($CuerpoJson['invoice']['retentions'], array(
                    "tax_category" => "RET_FUENTE",
                    "tax_rate" => $factura->getReteFuente()
                ));
            }
        }

        if ($factura->getDescuento() > 0) {
            $CuerpoJson['invoice']['charges'] = array(
                array(
                    'base_amount' => (float) $factura->getDescuento(),
                    'reason' => 'descuento',
                    'discount' => true
                )
            );
        }

        $this->llamarFacturaDataico($CuerpoJson, $factura, $entityManager);

        if ($factura->getCufe() != null && $factura->getCufe() != '') {
            $enviar->enviar($factura->getId());
        }



        $responseData = array(
            "results" => 'success',
        );
        return $this->json($responseData);
    }

    public function llamarFacturaDataico($Json, Factura $factura, EntityManagerInterface $entityManager)
    {
        //lamada a identificarse
        $prueba = 0;

        $Json['invoice']['dataico_account_id'] = '01814067-cc44-808a-83a1-de850ba1e360';
        $headers = array('Content-Type' => 'application/json', 'auth-token' => '232828f7e45e42e74ac28a0e0dbe4053');

        $Json['invoice']['env'] = 'PRODUCCION';



        $factura->setCuerpoJsonf(json_encode($Json));


        try {
            $client = new  GuzzleHttp\Client();
            $guzzleResult = $client->post('https://api.dataico.com/direct/dataico_api/v2/invoices', [
                'headers' => $headers,
                'body' => json_encode($Json)
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $guzzleResult = $e->getResponse();
        }


        $respuesta = $guzzleResult->getBody()->getContents();
        $factura->setRespuestaDian($respuesta);

        $respuestaServer =  json_decode($respuesta, true);




        $entityManager->persist($factura);
        $entityManager->flush();



        if (array_key_exists('dian_status', $respuestaServer)) {
            if ($respuestaServer['dian_status'] == 'DIAN_ACEPTADO' || $prueba == 1) {
                $factura->setCufe($respuestaServer['cufe']);

                $factura->setPdf($respuestaServer['uuid']);
                $date = new DateTime();
                $factura->setFechaValidacion($date);


                if ($respuestaServer['qrcode'] != null && $respuestaServer['qrcode'] != '') {
                    $codesDir = "uploads/assets/codes/";
                    $codeFile = $factura->getId() . '.png';
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

                    $factura->setEstado(1);
                }

                $entityManager->persist($factura);
                $entityManager->flush();
            }
        }

        if (array_key_exists('errors', $respuestaServer)) {
            $error = $respuestaServer['errors'][0];
            $prefijo = $factura->getFacturaResolucion()->getPrefijo();
            $numero = $factura->getNumeroFactura();
            if ($error['error'] == "Solo puede modificar Factura '" . $prefijo . $numero . "' si esta DIAN rechazada.") {

              

                $cuerpo['actions']['send_dian'] = true;
                $cuerpo['actions']['send_email'] = false;
                
              



                try {
                    $client = new  GuzzleHttp\Client();
                    $guzzleResult = $client->get('https://api.dataico.com/direct/dataico_api/v2/invoices?number=' . $prefijo . $numero, [
                        'headers' => $headers
                    ]);
                    
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    $guzzleResult = $e->getResponse();
                }
               
                $respuestaServerMetodoGet1 = json_decode($guzzleResult->getBody()->getContents(), true);
                

                if ($respuestaServerMetodoGet1['invoice']['dian_status'] != 'DIAN_ACEPTADO') {
                    $uuid = $respuestaServerMetodoGet1['invoice']['uuid'];
                    

                    try {
                        $client = new  GuzzleHttp\Client();
                        $guzzleResult = $client->put('https://api.dataico.com/direct/dataico_api/v2/invoices/' . $uuid, [
                            'headers' => $headers,
                            'body' => json_encode($cuerpo),
                        ]);
                    } catch (\GuzzleHttp\Exception\RequestException $e) {
                        $guzzleResult = $e->getResponse();
                    }
                }
                

                try {
                    $client = new  GuzzleHttp\Client();
                    $guzzleResult = $client->get('https://api.dataico.com/direct/dataico_api/v2/invoices?number=' . $prefijo . $numero, [
                        'headers' => $headers
                    ]);
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                    $guzzleResult = $e->getResponse();
                }

                $respuestaServerMetodoGet2 = json_decode($guzzleResult->getBody()->getContents(), true);

                if ($respuestaServerMetodoGet2['invoice']['dian_status'] == 'DIAN_ACEPTADO') {

                    $factura->setRespuestaDian(json_encode($respuestaServerMetodoGet2['invoice']));

                    $factura->setCufe($respuestaServerMetodoGet2['invoice']['cufe']);

                    $factura->setPdf($respuestaServerMetodoGet2['invoice']['uuid']);
                    $date = new DateTime();
                    $factura->setFechaValidacion($date);

                    if ($respuestaServerMetodoGet2['invoice']['qrcode'] != null && $respuestaServerMetodoGet2['invoice']['qrcode'] != '') {
                        $codesDir = "uploads/assets/codes/";
                        $codeFile = $factura->getId() . '.png';
                        $result = Builder::create()
                            ->writer(new PngWriter())
                            ->writerOptions([])
                            ->data($respuestaServerMetodoGet2['invoice']['qrcode'])
                            ->encoding(new Encoding('UTF-8'))
                            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                            ->size(300)
                            ->margin(1)
                            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                            ->labelText('')
                            ->labelFont(new NotoSans(20))
                            ->labelAlignment(new LabelAlignmentCenter())
                            ->build();

                        $result->saveToFile($codesDir . $codeFile);

                        $factura->setEstado(1);
                    }
                    $entityManager->persist($factura);
                    $entityManager->flush();
                }
            }
        }
    }
}
