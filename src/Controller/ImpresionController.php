<?php

namespace App\Controller;

use App\Entity\Envio;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PdfPersonalisado;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

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

        // Set some content to 
        
        $empresa = array(
            'nombre'=>'COMERCIALIZADORA BAMBUC FORWARDER',
            'tipoDoc'=>'NIT',
            'numero' =>'1098754989',
            'email'  => 'bambuc.forwarder@gmail.com',
            'telefono' => '3164388280',
            'direccion' => 'AV 87 22 11 IN 2 BRR DIAMANTE II',
            'ciudad' => 'BUCARAMANGA, SANTANDER (CO)'

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

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('factura.pdf', 'I');
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

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('medidas_envio.pdf', 'I');
    }
}
