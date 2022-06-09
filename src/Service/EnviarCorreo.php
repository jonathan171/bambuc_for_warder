<?php

namespace App\Service;

use App\Entity\Factura;
use App\Entity\FacturaItems;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp;

use Twig\Environment;



class EnviarCorreo
{
    private $entityManager;
    private $templating;

    public function __construct(EntityManagerInterface $entityManager, Environment $templating)
    {
        $this->entityManager = $entityManager;
        $this->templating = $templating;
    }

    public function enviar($id)
    {

        $factura = $this->entityManager->getRepository(Factura::class)->find($id);
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
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

        $pdf->setCufe('CUFE: ' . $factura->getCufe());


        // set text shadow effect
        $pdf->setTextShadow(array('enabled' => false, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to 

        $empresa = array(
            'nombre' => 'COMERCIALIZADORA BAMBUC FORWARDER',
            'tipoDoc' => 'NIT',
            'numero' => '1098754989',
            'email'  => 'bambuc.forwarder@gmail.com',
            'telefono' => '3164388280',
            'direccion' => 'AV 87 22 11 IN 2 BRR DIAMANTE II',
            'ciudad' => 'BUCARAMANGA, SANTANDER (CO)'

        );
        $items = $this->entityManager->getRepository(FacturaItems::class)->createQueryBuilder('fi')
            ->andWhere('fi.facturaClientes = :val')
            ->setParameter('val', $factura->getId())
            ->getQuery()->getResult();

        if (file_exists('uploads/assets/codes/' . $factura->getId() . '.png')) {
            $pha_img = 'uploads/assets/codes/' . $factura->getId() . '.png';
        } else {
            $pha_img = '';
        }
        $countItem = count($items);



        $html = $this->templating->render('impresion/factura.html.twig', [
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
        $pdfCorreo = $pdf->Output('Factura.pdf', 'S');

        $headers = array('Content-Type' => 'application/json', 'auth-token' => 'a4afb1e20e856e8fb031b487efbfd239');

        $data = array("actions" => array('send_dian' => false, 'send_email' => true, 'pdf' => base64_encode($pdfCorreo)));
        try {
            $client = new  GuzzleHttp\Client();
            $guzzleResult = $client->put('https://api.dataico.com/direct/dataico_api/v2/invoices/' . $factura->getPdf(), [
                'headers' => $headers,
                'body' => json_encode($data)
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $guzzleResult = $e->getResponse();
        }


        $respuesta = $guzzleResult->getBody()->getContents();
        $factura->setPdf($respuesta);

        $this->entityManager->persist($factura);
        $this->entityManager->flush();
    }
}
