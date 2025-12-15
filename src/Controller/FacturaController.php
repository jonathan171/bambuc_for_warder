<?php

namespace App\Controller;

use App\Entity\CondicionPago;
use App\Entity\Empresa;
use App\Entity\Envio;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\FacturaResolucion;
use App\Entity\UnidadesMedida;
use App\Form\FacturaType;
use App\Repository\ClientesRepository;
use App\Repository\FacturaRepository;
use App\Service\EnviarCorreo;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/factura')]
class FacturaController extends AbstractController
{
    #[Route('/', name: 'app_factura_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $companys = $entityManager->getRepository(Empresa::class)->createQueryBuilder('e')
        ->orderBy('e.id', 'ASC')
        ->getQuery()->getResult();

        return $this->render('factura/index.html.twig', [
            'companys' => $companys
        ]);
    }

    #[Route('/new', name: 'app_factura_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $factura = new Factura();
        $condicionPago = $entityManager
            ->getRepository(CondicionPago::class)
            ->find(77);
        $hora = new DateTime();
        $factura->setCondDePago($condicionPago);
        $factura->setTipoFactura('FACTURA_VENTA');
        $factura->setHora($hora);


        $form = $this->createForm(FacturaType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numeroQuery = $entityManager->getRepository(Factura::class)->createQueryBuilder('c')
                ->andWhere('c.facturaResolucion = :val')
                ->setParameter('val', $factura->getFacturaResolucion()->getId())
                ->orderBy('c.numeroFactura', 'DESC')
                ->setMaxResults(1);

            $facturaResolucion = $entityManager->getRepository(FacturaResolucion::class)->find($factura->getFacturaResolucion()->getId());

            $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

            if ($consulta) {
                $factura->setNumeroFactura($consulta->getNumeroFactura() + 1);
            } else {
                $factura->setNumeroFactura($facturaResolucion->getInicioConsecutivo());
            }


            $entityManager->persist($factura);
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('factura/new.html.twig', [
            'factura' => $factura,
            'form' => $form,
        ]);
    }

    #[Route('/buscadorAjaxCliente', name: 'app_factura_buscador_ajax_cliente', methods: ['GET', 'POST'])]
    public function executeBuscadorAjaxCliente(
        Request $request,
        ClientesRepository $clienteRepository
    ) {
        $busqueda = $request->query->get('term');



        $start = 0;
        $length = 20;


        $data_table  = $clienteRepository->findByDataShearch([
            'page' => ($start / $length),
            'pageSize' =>  $length,
            'search' =>  $busqueda
        ]);


        $responseData = array(
            "results" => $data_table['data'],
            "pagination" => array(
                // Determinar si hay mas paginas disponibles
                "more" => (false)
            )
        );
        return $this->json($responseData);
    }

    #[Route('/buscarConsecutivo', name: 'app_factura_buscar_consecutivo', methods: ['GET', 'POST'])]
    public function executeBuscarConsecutivo(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $numeroQuery = $entityManager->getRepository(Factura::class)->createQueryBuilder('c')
            ->andWhere('c.facturaResolucion = :val')
            ->setParameter('val', $request->request->get('resolucion_id'))
            ->orderBy('c.numeroFactura', 'DESC')
            ->setMaxResults(1);

        $facturaResolucion = $entityManager->getRepository(FacturaResolucion::class)->find($request->request->get('resolucion_id'));

        $consulta = $numeroQuery->getQuery()->getOneOrNullResult();

        if ($consulta) {
            $numero = $consulta->getNumeroFactura() + 1;
        } else {
            $numero = $facturaResolucion->getInicioConsecutivo();
        }

        $responseData = array(
            "results" => $numero,
        );
        return $this->json($responseData);
    }

    #[Route('/table', name: 'app_factura_table', methods: ['GET', 'POST'])]
    public function table(Request $request, EntityManagerInterface $entityManager, FacturaRepository $facturaRepository): Response
    {
        // Unificamos GET / POST
        $params = $request->isMethod('POST')
            ? $request->request
            : $request->query;

        // Search
        $searchArr   = $params->all('search');
        $searchValue = $searchArr['value'] ?? '';

        // Paginación
        $start  = (int) $params->get('start', 0);
        $length = (int) $params->get('length', 10);
        $page   = $length > 0 ? (int) floor($start / $length) : 0;

        // Columnas y orden
        $columns = $params->all('columns');
        $order   = $params->all('order');

        $orderBy = null;
        if (!empty($order) && isset($order[0])) {
            $columnIndex = $order[0]['column'];
            $orderBy = [
                'column' => $columns[$columnIndex]['data'] ?? null,
                'dir'    => $order[0]['dir'] ?? 'asc',
            ];
        }

        // Consulta al repositorio
        $data_table = $facturaRepository->findByDataTable([
            'page'     => $page,
            'pageSize' => $length,
            'search'   => $searchValue,
            'order'    => $orderBy,
        ]);

        // Respuesta para DataTables
        return $this->json([
            'draw'            => (int) $params->get('draw', 1),
            'recordsTotal'    => $data_table['totalRecords'],
            'recordsFiltered' => $data_table['totalRecords'],
            'data'            => $data_table['data'],
        ]);
    }

    #[Route('/{id}/show', name: 'app_factura_show', methods: ['GET'])]
    public function show(Factura $factura): Response
    {
        return $this->render('factura/show.html.twig', [
            'factura' => $factura,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_factura_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Factura $factura, EntityManagerInterface $entityManager): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(FacturaType::class, $factura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $factura->setTotalReteIca($factura->getSubtotal() * ($factura->getReteIca() / 100));
            $factura->setTotalReteFuenteG($factura->getSubtotal() * ($factura->getReteFuente() / 100));
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $entityManager->flush();

            return $this->redirectToRoute('app_factura_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
        }
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        return $this->renderForm('factura/edit.html.twig', [
            'factura' => $factura,
            'form' => $form,
            'items' => $items,

        ]);
    }
    //facturar todos los evios que han sido seleccionados

    #[Route('/facturar_envios', name: 'app_factura_facturar_envios', methods: ['GET', 'POST'])]
    public function executeFacturarEnvios(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $enviosId = (array) $request->request->get('envId');
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('factura_id'));
        $unidad =  $entityManager->getRepository(UnidadesMedida::class)->find(67);

        foreach ($enviosId as $envioId) {

            $envio = $entityManager->getRepository(Envio::class)->find($envioId);

            $item = new FacturaItems();
            $item->setCantidad(1);
            $item->setDescripcion('TRANSPORTE COURRIER');
            $item->setValorUnitario($envio->getTotalACobrar());
            $item->setSubtotal($envio->getTotalACobrar());
            $item->setIva(0);
            $item->setValorIva(0);
            $item->setTotal($envio->getTotalACobrar());
            $item->setFacturaClientes($factura);
            $item->setCodigo($envio->getNumeroEnvio());
            $item->setRetencionFuente(0);
            $item->setValorRetencionFuente(0);
            $item->setTasaDescuento(0);
            $item->setValorDescuento(0);
            $item->setUnidad($unidad);

            $entityManager->persist($item);
            $entityManager->flush();

            $envio->setFacturado(1);
            $envio->setFacturaItems($item);

            $entityManager->persist($envio);
            $entityManager->flush();

            $factura->setSubtotal($factura->getSubtotal() + $item->getSubtotal());
            $factura->setTotal($factura->getTotal() + $item->getTotal());
            $factura->setTotalIva($factura->getTotalIva() +  $item->getValorIva());
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $factura->setTotalReteIca(($factura->getReteIca() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuenteG(($factura->getReteFuente() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuente($factura->getTotalReteFuente() + $item->getValorRetencionFuente());


            $entityManager->persist($factura);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cargar_items', name: 'app_factura_cargar_items', methods: ['GET', 'POST'])]
    public function executeCargarItems(
        Request $request,
        EntityManagerInterface $entityManager
    ) {

        $datos = json_decode($request->request->get('datos'));
        $total = 0;

        foreach ($datos as $dato) {
            if ($dato != null && $dato != '') {

                $total = $dato[4]->valor;
                if ($dato[1]->valor != '' && $dato[1]->valor != null) {
                    $valorDescuento = ($dato[1]->valor / 100) * $dato[5]->valor;
                    $dato[5]->valor -= $valorDescuento;
                    $total = $dato[5]->valor;
                } else {
                    $valorDescuento = 0;
                }

                if ($dato[3]->valor != '' && $dato[3]->valor != null) {
                    $valorIva = (($dato[3]->valor / 100)) * $dato[5]->valor;
                    $total = (($dato[3]->valor / 100) + 1) * $dato[5]->valor;
                } else {
                    $dato[3]->valor = 0;
                    $valorIva = 0;
                }

                if ($dato[2]->valor != '' && $dato[2]->valor != null) {
                    $valorReFue = ($dato[2]->valor / 100) * $dato[5]->valor;
                    $total -= $valorReFue;
                } else {
                    $valorReFue = 0;
                }

                $item = $entityManager->getRepository(FacturaItems::class)->find($request->request->get('id'));

                $item->setDescripcion($dato[0]->valor);
                $item->setSubtotal($dato[5]->valor);
                $item->setIva($dato[3]->valor);
                $item->setValorIva($valorIva);
                $item->setTotal($total);
                $item->setRetencionFuente($dato[2]->valor);
                $item->setValorRetencionFuente($valorReFue);
                $item->setTasaDescuento($dato[1]->valor);
                $item->setValorDescuento($valorDescuento);
                $entityManager->persist($item);
                $entityManager->flush();
            }
        }
        $factura = $item->getFacturaClientes();
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        $total_factura1 = 0;
        $totalIva = 0;
        $totalFuente = 0;
        $subtotal = 0;
        foreach ($items as $item) {
            $total_factura1 = $total_factura1 + $item->getTotal();
            $totalIva += $item->getValorIva();
            $totalFuente += $item->getValorRetencionFuente();
            $subtotal += $item->getSubtotal();
        }
        $factura->setTotal($total_factura1);
        $factura->setSubtotal($subtotal);
        $factura->setTotalIva($totalIva);
        $factura->setTotalReteIva($totalIva * ($factura->getReteIva() / 100));
        $factura->setTotalReteIca($subtotal * ($factura->getReteIca() / 100));
        $factura->setTotalReteFuenteG($subtotal * ($factura->getReteFuente() / 100));
        $factura->setTotalReteFuente($totalFuente);
        $entityManager->persist($factura);
        $entityManager->flush();


        $thearray[0] = number_format($total, 2, '.', '');
        $thearray[1] = $request->request->get('id');
        $thearray[2] = number_format($total_factura1 - ($factura->getTotalReteIca() + $factura->getTotalReteIva()), 2, '.', '');


        return $this->json($thearray);
    }
    #[Route('/reportar', name: 'app_factura_reportar', methods: ['GET', 'POST'])]
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

    #[Route('item_delete/{id}', name: 'app_factura_item_delete', methods: ['POST'])]
    public function itemDelete(Request $request, FacturaItems $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {

            $factura = $entityManager->getRepository(Factura::class)->find($item->getFacturaClientes()->getId());
            $factura->setSubtotal($factura->getSubtotal() - $item->getSubtotal());
            $factura->setTotal($factura->getTotal() - $item->getTotal());
            $factura->setTotalIva($factura->getTotalIva() - $item->getValorIva());
            $factura->setTotalReteIva($factura->getTotalIva() * ($factura->getReteIva() / 100));
            $factura->setTotalReteIca(($factura->getReteIca() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuenteG(($factura->getReteFuente() / 100) * $factura->getSubtotal());
            $factura->setTotalReteFuente($factura->getTotalReteFuente() - $item->getValorRetencionFuente());

            $entityManager->persist($factura);
            $entityManager->flush();


            $envio = $entityManager->getRepository(Envio::class)->findOneBy(['facturaItems' => $item->getId()]);
            $envio->setFacturado(0);
            $envio->setFacturaItems(NULL);
            $entityManager->persist($envio);
            $entityManager->flush();


            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_edit', ['id' => $factura->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/error_dian', name: 'app_factura_error_dian', methods: ['GET', 'POST'])]
    public function erroresDian(Request $request, EntityManagerInterface $entityManager): Response
    {
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('idFactura'));

        return $this->render('factura/error_dian.html.twig', [
            'id'     => $factura->getId(),
            'respuesta'   => html_entity_decode($factura->getRespuestaDian()),
            'respuestaPdf' => html_entity_decode($factura->getRespuestaCorreo())
        ]);
    }

    #[Route('/{id}/delete', name: 'app_factura_delete', methods: ['POST'])]
    public function delete(Request $request, Factura $factura, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $factura->getId(), $request->request->get('_token'))) {
            $entityManager->remove($factura);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_factura_index', [], Response::HTTP_SEE_OTHER);
    }

    public function llamarFacturaDataico($Json, Factura $factura, EntityManagerInterface $entityManager)
    {
        //lamada a identificarse
        $prueba = 0;
        $empresa = $factura->getFacturaResolucion()->getEmpresa();

        $Json['invoice']['dataico_account_id'] = $empresa->getUsuario();
        $headers = array('Content-Type' => 'application/json', 'auth-token' => $empresa->getClave());

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

    #[Route('/saveSoporte', name: 'app_factura_save_soporte', methods: ['GET', 'POST'])]
    public function executeSaveSoporte(
        Request $request,
        EntityManagerInterface $entityManager,
        FileUploader $fileUploader,
        ParameterBagInterface $params
    ) {
        $archivo = $request->files->get('file');
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('data_id'));

        if ($archivo) {
            $fileName = $fileUploader
                    ->setTargetDirectory($params->get("upload_dir_images"))
                    ->upload($archivo);

                $factura->setSoportePago($fileName);
                $entityManager->persist($factura);
                $entityManager->flush();
        } 
       
        $responseData = array(
            "results" =>  $fileName,
        );
        return $this->json($responseData);
    }

    #[Route('/verificar', name: 'app_factura_verificar', methods: ['GET', 'POST'])]
    public function  verificar(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('id'));

        $factura->setFacturado(1);

        $entityManager->persist( $factura);
        $entityManager->flush();
   
        $responseData = array(
        "results" => 'success',
        );
         return $this->json($responseData);
    }

    #[Route('/desverificar', name: 'app_factura_desverificar', methods: ['GET', 'POST'])]
    public function  desverificar(
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        
        $factura = $entityManager->getRepository(Factura::class)->find($request->request->get('id'));

        $factura->setFacturado(0);

        $entityManager->persist($factura);
        $entityManager->flush();
   
        $responseData = array(
        "results" => 'success',
        );
         return $this->json($responseData);
    }

}
