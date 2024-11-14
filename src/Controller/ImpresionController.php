<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\NotaCredito;
use App\Entity\NotaCreditoItems;
use App\Entity\ReciboCaja;
use App\Entity\ReciboCajaItem;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PdfPersonalisado;
use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;
use Picqer\Barcode\BarcodeGeneratorPNG;

#[Route('/impresion')]
class ImpresionController extends AbstractController
{
    #[Route('/', name: 'app_impresion')]
    public function index():Response
    {
        return $this->render('impresion/factura.html.twig');
        
    }

    #[Route('/impresion_factura', name: 'app_impresion_factura', methods: ['GET'])]
    public function factura(Request $request, EntityManagerInterface $entityManager)
    {

        $factura = $entityManager->getRepository(Factura::class)->find($request->query->get('id'));
        // create new PDF document
        $pdf = new PdfPersonalisado(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Factura');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 9, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);



        $pdf->AddPage();

        $pdf->setCufe('CUFE: '.$factura->getCufe());


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        //
        $empresa_base = $factura->getFacturaResolucion()->getEmpresa();
        // Set some content to 
        if($empresa_base->getId()==1){
            $email = 'bambuc.forwarder@gmail.com';
            $iva = 'No somos Agente Retenedor del Impuesto sobre las Ventas - IVA';
            $obligacion = 'NO RESPONSABLE DE IVA';
        }else{
            $email = 'comercializadorabambucsas@gmail.com';
            $iva = 'Agente Retenedor del Impuesto sobre las Ventas - IVA';
            $obligacion = 'RESPONSABLE DE IVA';
        }
        
        $empresa = array(
            'nombre'=>$empresa_base->getNombre(),
            'tipoDoc'=>$empresa_base->getTipoDoc(),
            'numero' =>$empresa_base->getDocumento(),
            'email'  => $email,
            'telefono' => '3164388280',
            'direccion' => 'AV 87 22 11 IN 2 BRR DIAMANTE II',
            'ciudad' => 'BUCARAMANGA, SANTANDER (CO)',
            'iva'    => $iva,
            'obligacion' => $obligacion

        );
        $items = $entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        if(file_exists('uploads/assets/codes/'.$factura->getId().'.png')) {
            $pha_img = 'uploads/assets/codes/'.$factura->getId().'.png';

        }else {
            $pha_img = '';
        }
        $countItem= count($items);

        if($request->query->get('html')){

            return $this->render('impresion/factura.html.twig', [
                'factura' => $factura,
                'empresa' => $empresa,
                'code' =>   $pha_img,
                'items' =>   $items,
                'countItem' => $countItem,
            ]);
        }

        $html = $this->renderView('impresion/factura.html.twig', [
            'factura' => $factura,
            'empresa' => $empresa,
            'code' => $pha_img,
            'items' => $items,
            'countItem' => $countItem,
        ]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------
        $fileName = $factura->getFacturaResolucion()->getPrefijo().'-'.$factura->getNumeroFactura();
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($fileName.'.pdf', 'I');
    }

    #[Route('/impresion_nota', name: 'app_impresion_nota', methods: ['GET'])]
    public function nota(Request $request, EntityManagerInterface $entityManager)
    {

        $nota = $entityManager->getRepository(NotaCredito::class)->find($request->query->get('id'));
        // create new PDF document
        $pdf = new PdfPersonalisado(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Nota');
        $pdf->SetSubject('Impresion Nota');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 9, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);



        $pdf->AddPage();

        $pdf->setCufe('CUFE: '.$nota->getCufe());


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to 
        $factura = $nota->getFacturaCliente();
        $empresa_base = $factura->getFacturaResolucion()->getEmpresa();
        // Set some content to 
        if($empresa_base->getId()==1){
            $email = 'bambuc.forwarder@gmail.com';
            $iva = 'No somos Agente Retenedor del Impuesto sobre las Ventas - IVA';
            $obligacion = 'NO RESPONSABLE DE IVA';
        }else{
            $email = 'comercializadorabambucsas@gmail.com';
            $iva = 'Agente Retenedor del Impuesto sobre las Ventas - IVA';
            $obligacion = 'RESPONSABLE DE IVA';
        }
        
        $empresa = array(
            'nombre'=>$empresa_base->getNombre(),
            'tipoDoc'=>$empresa_base->getTipoDoc(),
            'numero' =>$empresa_base->getDocumento(),
            'email'  => $email,
            'telefono' => '3164388280',
            'direccion' => 'AV 87 22 11 IN 2 BRR DIAMANTE II',
            'ciudad' => 'BUCARAMANGA, SANTANDER (CO)',
            'iva'    => $iva,
            'obligacion' => $obligacion

        );
        $items = $entityManager->getRepository(NotaCreditoItems::class)->createQueryBuilder('nci')
            ->andWhere('nci.notaCredito = :val')
            ->setParameter('val', $nota->getId())
            ->getQuery()->getResult();
        $factura = $nota->getFacturaCliente();

        if(file_exists('uploads/assets/codes/N'.$nota->getId().'.png')) {
            $pha_img = 'uploads/assets/codes/N'.$nota->getId().'.png';

        }else {
            $pha_img = '';
        }
        $countItem= count($items);
        if($nota->getTipo()=='credito'){
            $tipo = 'Nota Crédito';
            $prefijo = 'NC';
        } else {
            $tipo = 'Nota Debito';
            $prefijo = 'ND';
        }

        if($request->query->get('html')){

            return $this->render('impresion/nota.html.twig', [
                'factura' => $factura,
                'empresa' => $empresa,
                'code' =>   $pha_img,
                'items' =>   $items,
                'countItem' => $countItem,
                'nota' => $nota,
                'tipo' => $tipo,
                'prefijo' => $prefijo
            ]);
        }

        $html = $this->renderView('impresion/nota.html.twig', [
            'factura' => $factura,
            'empresa' => $empresa,
            'code' => $pha_img,
            'items' => $items,
            'countItem' => $countItem,
            'nota' => $nota,
            'tipo' => $tipo,
            'prefijo' => $prefijo
        ]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------
        $fileName = 'Nota'.$nota->getNumeroNota();
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output( $fileName.'.pdf', 'I');
    }


    #[Route('/impresion_dimension_envio', name: 'app_impresion_dimension_envio', methods: ['GET'])]
    public function dimencionEnvio(Request $request, EntityManagerInterface $entityManager)
    {

        $envio = $entityManager->getRepository(Envio::class)->find($request->query->get('id'));
        // create new PDF document
        $pdf = new TCPDF('L', PDF_UNIT, 'A5', true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Envio');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 9, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);
        $pdf->setPrintFooter(false);



        $pdf->AddPage();

        


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to 
        
        
        $jsonEnvio = json_decode($envio->getJsonRecibido(),true);
        if($request->query->get('html')){

            return $this->render('impresion/medida_envio.html.twig', [
                'Json'  => $jsonEnvio['shipments'][0],
                'envio' => $envio
            ]);
        }

        $html = $this->renderView('impresion/medida_envio.html.twig', [
            'Json'  => $jsonEnvio['shipments'][0],
            'envio' => $envio
        ]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------
        $fileName = $envio->getNumeroEnvio();
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('medidas_'.$fileName.'.pdf', 'I');
    }

    #[Route('/impresion_remision', name: 'app_impresion_remision', methods: ['GET'])]
    public function impresionRemision(Request $request, EntityManagerInterface $entityManager)
    {

        $remision = $entityManager->getRepository(EnviosNacionales::class)->find($request->query->get('id'));

        // create new PDF document
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        // Enable the loading of remote resources
        $path = 'assets/images/facturas/2.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $path_vertical_origen = 'assets/images/facturas/origen-vertical.png';
        $type_vertical_origen = pathinfo($path_vertical_origen, PATHINFO_EXTENSION);
        $data_vertical_origen = file_get_contents($path_vertical_origen);
        $base64_vertical_origen = 'data:image/' . $type_vertical_origen . ';base64,' . base64_encode($data_vertical_origen);
       
        $path_vertical_destino = 'assets/images/facturas/destino-vertical.png';
        $type_vertical_destino = pathinfo($path_vertical_destino, PATHINFO_EXTENSION);
        $data_vertical_destino = file_get_contents($path_vertical_destino);
        $base64_vertical_destino = 'data:image/' . $type_vertical_destino . ';base64,' . base64_encode($data_vertical_destino);

        $path_flecha = 'assets/images/facturas/flecha.png';
        $type_flecha = pathinfo($path_flecha, PATHINFO_EXTENSION);
        $data_flecha = file_get_contents($path_flecha);
        $base64_flecha = 'data:image/' . $type_flecha . ';base64,' . base64_encode($data_flecha);
        
        
       
        if($request->query->get('html')){

            return $this->render('impresion/remision.html.twig', [
                'remision' => $remision,
                'base64' =>  $base64,
                'base64_vertical_origen' =>$base64_vertical_origen,
                'base64_vertical_destino' =>$base64_vertical_destino,
                'base64_flecha' => $base64_flecha
            ]);
        }

        $html = $this->renderView('impresion/remision.html.twig', [
            'remision' => $remision,
            'base64' =>  $base64,
            'base64_vertical_origen' =>$base64_vertical_origen,
            'base64_vertical_destino' =>$base64_vertical_destino,
            'base64_flecha' => $base64_flecha
        ]);

        // Print text using writeHTMLCell()
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', '');
       
        $pdf->render();

        // ---------------------------------------------------------
        $fileName = 'RM'.$remision->getNumero();
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->stream( $fileName.'.pdf', ['Attachment' => false]);
    }

    #[Route('/impresion_stiker', name: 'app_impresion_stiker', methods: ['GET'])]
    public function stiker(Request $request, EntityManagerInterface $entityManager)
    {

        $unidad = $entityManager->getRepository(EnviosNacionalesUnidades::class)->find($request->query->get('id'));

        $items = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('fi')
        ->andWhere('fi.envioNacional = :val')
        ->setParameter('val', $unidad->getEnvioNacional()->getId())
        ->getQuery()->getArrayResult();
        $found_key = array_search($unidad->getId(), array_column($items, 'id'));
        $totalItem = count($items);
        // create new PDF document
        $pdf = new TCPDF('P', PDF_UNIT, array(100,100), true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Stiker');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(2, 4, 2, 0);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(0);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE,0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 12, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);
        $pdf->setPrintFooter(false);



        $pdf->AddPage();

        


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to 
        
        $generator = new BarcodeGeneratorPNG();
        //$base_64='data:image/png;base64,';
        $base_64 = base64_encode($generator->getBarcode($unidad->getEnvioNacional()->getNumeroGuia() ? $unidad->getEnvioNacional()->getNumeroGuia() : $unidad->getNumeroGuia(), $generator::TYPE_CODE_128));
        
        
        /*$imageContent = file_get_contents($base_64);
        $path = tempnam(sys_get_temp_dir(), 'prefix');

        file_put_contents ($path, $imageContent);
        echo $path;
        die();*/

        $numero_guia = $unidad->getEnvioNacional()->getNumeroGuia() ? $unidad->getEnvioNacional()->getNumeroGuia() : $unidad->getNumeroGuia();
        if($request->query->get('html')){

            return $this->render('impresion/stiker.html.twig', [
                'unidad'  => $unidad,
                'remision' => $unidad->getEnvioNacional(),
                'numero_guia' => $numero_guia,
                'base_64'  => $base_64,
                'key' => $found_key+1,
            ]); 
        }

        $html = $this->renderView('impresion/stiker.html.twig', [
            'unidad'  => $unidad,
            'remision' => $unidad->getEnvioNacional(),
            'base_64'  => '@'.$base_64,
            'key' => ($found_key+1).'/'.$totalItem,
        ]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($numero_guia.'.pdf', 'I');
    }

    #[Route('/impresion_stiker_todos', name: 'app_impresion_stiker_todos', methods: ['GET','POST'])]
    public function stikerTodos(Request $request, EntityManagerInterface $entityManager)
    {
        $datos = (array)$request->request->get('datos');
        
        // create new PDF document
        $pdf = new TCPDF('P', PDF_UNIT, array(100,100), true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Stiker');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(2, 4, 2, 0);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(0);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE,0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 12, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);
        $pdf->setPrintFooter(false);



        $pdf->AddPage();

        


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to 
        
        $generator = new BarcodeGeneratorPNG();
        //$base_64='data:image/png;base64,';
       
        
        
        /*$imageContent = file_get_contents($base_64);
        $path = tempnam(sys_get_temp_dir(), 'prefix');

        file_put_contents ($path, $imageContent);
        echo $path;
        die();*/
        $html = '';
        $i = 1;
        $unidad_1 = 0;
        $unidad_fin = 0;
        foreach($datos as $dato){
            if($i ==1){
                $unidad_1 = $dato['remision'];
                $i++;
            }
            $unidad_fin =  $dato['remision'];
            $items = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('fi')
            ->andWhere('fi.envioNacional = :val')
            ->setParameter('val', $dato['remision'])
            ->getQuery()->getResult();
            
            $items_array = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('fi')
            ->andWhere('fi.envioNacional = :val')
            ->setParameter('val', $dato['remision'])
            ->getQuery()->getArrayResult();
            $totalItem = count($items);
            foreach ($items as $unidad){
        
                $found_key = array_search($unidad->getId(), array_column($items_array, 'id'));
               
                $base_64 = base64_encode($generator->getBarcode($unidad->getEnvioNacional()->getNumeroGuia() ? $unidad->getEnvioNacional()->getNumeroGuia() : $unidad->getNumeroGuia(), $generator::TYPE_CODE_128));
                /*if($request->query->get('html')){
        
                    return $this->render('impresion/stiker.html.twig', [
                        'unidad'  => $unidad,
                        'remision' => $unidad->getEnvioNacional(),
                        'base_64'  => $base_64,
                        'key' => $found_key+1,
                    ]); 
                }*/
                
                $html .= $this->renderView('impresion/stiker.html.twig', [
                    'unidad'  => $unidad,
                    'remision' => $unidad->getEnvioNacional(),
                    'numero_guia' => $unidad->getEnvioNacional()->getNumeroGuia() ? $unidad->getEnvioNacional()->getNumeroGuia() : $unidad->getNumeroGuia(),
                    'base_64'  => '@'.$base_64,
                    'key' => ($found_key+1).'/'.$totalItem,
                ]);

            }
           
        }
      

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('stiker'.$unidad_1.'-'.$unidad_fin.'.pdf', 'I');
    }

    #[Route('/impresion_remision_todos', name: 'app_impresion_remision_todos', methods: ['GET','POST'])]
    public function impresionRemisionTodos(Request $request, EntityManagerInterface $entityManager)
    {

        $datos = (array)$request->request->get('datos');
       

        // create new PDF document
        $options = new Options();
        $options->set('isRemoteEnabled', true);
     
        $pdf = new Dompdf($options);
        // Enable the loading of remote resources
        $path = 'assets/images/facturas/2.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $path_vertical_origen = 'assets/images/facturas/origen-vertical.png';
        $type_vertical_origen = pathinfo($path_vertical_origen, PATHINFO_EXTENSION);
        $data_vertical_origen = file_get_contents($path_vertical_origen);
        $base64_vertical_origen = 'data:image/' . $type_vertical_origen . ';base64,' . base64_encode($data_vertical_origen);
       
        $path_vertical_destino = 'assets/images/facturas/destino-vertical.png';
        $type_vertical_destino = pathinfo($path_vertical_destino, PATHINFO_EXTENSION);
        $data_vertical_destino = file_get_contents($path_vertical_destino);
        $base64_vertical_destino = 'data:image/' . $type_vertical_destino . ';base64,' . base64_encode($data_vertical_destino);

        $path_flecha = 'assets/images/facturas/flecha.png';
        $type_flecha = pathinfo($path_flecha, PATHINFO_EXTENSION);
        $data_flecha = file_get_contents($path_flecha);
        $base64_flecha = 'data:image/' . $type_flecha . ';base64,' . base64_encode($data_flecha);

        // Set some content to 
        $html='';
        
        $i = 1;
        $unidad_1 = 0;
        $unidad_fin = 0;
        foreach($datos as $dato){
            if($i == 1){
                $unidad_1 = $dato['remision'];
                $i++;
            }
            $unidad_fin = $dato['remision'];
            $remision = $entityManager->getRepository(EnviosNacionales::class)->find($dato['remision']);

            $html.= $this->renderView('impresion/remision.html.twig', [
                'remision' => $remision,
                'base64' =>  $base64,
                'base64_vertical_origen' =>$base64_vertical_origen,
                'base64_vertical_destino' =>$base64_vertical_destino,
                'base64_flecha' => $base64_flecha
            ]);

        }
       
       

       // Print text using writeHTMLCell()
       $pdf->loadHtml($html);
      
      
       $pdf->render();

       // ---------------------------------------------------------

       // Close and output PDF document
       // This method has several options, check the source code documentation for more information.
       $pdf->stream('remision'.$unidad_1.'-'.$unidad_fin.'.pdf', ['Attachment' => false]);
    }

    #[Route('/impresion_recibo', name: 'app_impresion_recibo', methods: ['GET'])]
    public function recibo(Request $request, EntityManagerInterface $entityManager)
    {

        $recibo = $entityManager->getRepository(ReciboCaja::class)->find($request->query->get('id'));
        // create new PDF document
        $pdf = new PdfPersonalisado(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jonathan Cruz');
        $pdf->SetTitle('Impresion Recibo');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
        $pdf->setFooterData(array(0, 64, 0), array(0, 0, 128));

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(3, 10, 2);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('Helvetica', '', 9, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->SetPrintHeader(false);



        $pdf->AddPage();

       // $pdf->setCufe('CUFE: '.$factura->getCufe());


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        //
      
        $items = $entityManager->getRepository(ReciboCajaItem::class)->createQueryBuilder('ri')
            ->andWhere('ri.reciboCaja = :val')
            ->setParameter('val', $recibo->getId())
            ->getQuery()->getResult();

        $array_items = [];
        foreach($items as $item){
            $envio = $entityManager->getRepository(EnviosNacionales::class)->findOneBy(['reciboItems' => $item->getId()]);
            if(!$envio){
                $envio = $entityManager->getRepository(Envio::class)->findOneBy(['reciboItems' => $item->getId()]); 
            }else{
                $descripcion = '('.$envio->getFecha()->format('Y-m-d').')';
                $unidades= $entityManager->getRepository(EnviosNacionalesUnidades::class)->findBy(['envioNacional' => $envio->getId()]);
                $desRef = '';
                 foreach($unidades as $unidad){
                    $desRef .= $unidad->getNumeroGuia() ? $unidad->getNumeroGuia().' ': '';
                 }
            }

            $array_items[$item->getId()]['descripcion'] = $descripcion;
            $array_items[$item->getId()]['ref'] = $desRef;
        }
       
        $countItem= count($items);

        if($request->query->get('html')){

            return $this->render('impresion/recibo.html.twig', [
                'recibo' => $recibo,
                'creada_por' => $recibo->getCreadaPor(),
                'paga_a' => $recibo->getPagadaA(),
                'items' =>   $items,
                'countItem' => $countItem,
                'municipio' => $recibo->getMunicipio(),
                'unidades' => $array_items
            ]);
        }

        $html = $this->renderView('impresion/recibo.html.twig', [
                'recibo' => $recibo,
                'creada_por' => $recibo->getCreadaPor(),
                'paga_a' => $recibo->getPagadaA(),
                'items' =>   $items,
                'countItem' => $countItem,
                'municipio' => $recibo->getMunicipio(),
                'unidades' => $array_items
        ]);

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------
        $fileName = 'RE-'.$recibo->getNumeroRecibo();
        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($fileName.'.pdf', 'I');
    }
}
