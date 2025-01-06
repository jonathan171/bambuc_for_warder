<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\Envio;
use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use App\Entity\Factura;
use App\Entity\FacturaItems;
use App\Entity\Pais;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
// Incluir namespaces requeridos de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteEnvioController extends AbstractController
{
    #[Route('/reporte_envio', name: 'app_reporte_envio')]
    public function index(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('reporte_envio/index.html.twig', [
            'controller_name' => 'ReporteEnvioController',
        ]);
    }

    #[Route('/reporte_envio_nacional', name: 'app_reporte_envio_nacional')]
    public function reporteNacional(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('reporte_envio/envio_nacional.html.twig', [
            
        ]);
    }

    #[Route('/reporte_envio_excel', name: 'app_reporte_envio_excel')]
    public function excelEnvio(Request $request, EntityManagerInterface $entityManager): Response
    {
        $spreadsheet = new Spreadsheet();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('assets/images/facturas/logo.jpg');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(100);

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $drawing->setWorksheet($sheet);
        $sheet->getCell('A4')->setValue("#");
        $sheet->getCell('B4')->setValue("FECHA \n DE \n FACTURACIÓN");

        $sheet->getCell('C4')->setValue("GUÍA");
        $sheet->getCell('D4')->setValue("REMITENTE");
        $sheet->getCell('E4')->setValue("DESTINO");
        $sheet->getCell('F4')->setValue("PESO COBRADO");
        $sheet->getCell('G4')->setValue("DESTINATARIO");
        $sheet->getCell('H4')->setValue("VALOR \n DEL \n ENVÍO");
        $sheet->getCell('I4')->setValue("FACTURA");
        $sheet->getCell('J4')->setValue("RECIBO");
        $sheet->getCell('K4')->setValue("REFERENCIA");



        $styleArray = array(
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => array('argb' => '070707'),
                ),
            ),
        );
        foreach (range('A', 'K') as $columnID) {

            $sheet->getStyle($columnID . '4')->applyFromArray($styleArray);
        }

        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->getRowDimension('4')->setRowHeight(45, 'pt');
        $sheet->getRowDimension('1')->setRowHeight(100, 'px');
       






        $query = $entityManager->getRepository(Envio::class)->createQueryBuilder('e')
            ->innerJoin(Pais::class, 'p', Join::WITH,   'p.id = e.paisDestino')
            ->innerJoin(Pais::class, 'p1', Join::WITH,  'p1.id = e.paisOrigen')
            ->leftJoin(FacturaItems::class, 'fi', Join::WITH, 'fi.id = e.facturaItems')
            ->leftJoin(Factura::class, 'f', Join::WITH, 'f.id = fi.facturaClientes')
            ->andWhere('e.fechaEnvio >= :val')
            ->setParameter('val', $request->get('fecha_inicio'))
            ->andWhere('e.fechaEnvio <= :val1')
            ->setParameter('val1', $request->get('fecha_fin'));

        if ($request->get('quien_envia')) {

            $shearch = '%' . $request->get('quien_envia') . '%';
            $query->andWhere(' e.quienEnvia like :val2')
                ->setParameter('val2', $shearch);
        }
        if ($request->get('referencia')) {

            $shearch = '%' . $request->get('referencia') . '%';
            $query->andWhere(' e.referencia like :val4')
                ->setParameter('val4', $shearch);
        }
        if ($request->get('facturado')) {
            if($request->get('facturado')=='facturado'){
                $query->andWhere(' e.facturado = :val3')
                ->setParameter('val3', 1);
            }else{
                $query->andWhere(' e.facturado != :val3')
                ->setParameter('val3', 1);
            }
           
        }


        $envios = $query->getQuery()->getResult();

        // Indice de la celda en la que se comienza a renderizar
        $cell = 5;
        $i = 0;
        $total = 0;

        foreach ($envios as $envio) {
            $i++;

            $sheet->setCellValue("A$cell", ($i));
            $sheet->setCellValue("B$cell", $envio->getFechaEnvio()->format('d-M-y'));
            $sheet->setCellValue("C$cell", $envio->getNumeroEnvio());
            $sheet->setCellValue("D$cell", $envio->getQuienEnvia());
            $sheet->setCellValue("E$cell", $envio->getPaisDestino()->getNombre());
            $sheet->setCellValue("F$cell", $envio->getTotalPesoCobrar());
            $sheet->setCellValue("G$cell", $envio->getQuienRecibe());
            $sheet->setCellValue("H$cell", $envio->getTotalACobrar());
            if($envio->getFacturaItems()){
                $sheet->setCellValue("I$cell", $envio->getFacturaItems()->getFacturaClientes()->getFacturaResolucion()->getPrefijo().$envio->getFacturaItems()->getFacturaClientes()->getNumeroFactura());

            }
            if($envio->getReciboCajaItem()){
                $sheet->setCellValue("J$cell", "RE".$envio->getReciboCajaItem()->getReciboCaja()->getNumeroRecibo());
            }
            $sheet->getStyle("H$cell",)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->setCellValue("K$cell", $envio->getReferencia());
            $total += $envio->getTotalACobrar();
            foreach (range('A', 'K') as $columnID) {

                $sheet->getStyle($columnID . $cell)->applyFromArray($styleArray);
            }

            // Continuar en una nueva fila
            $cell++;
        }
        $sheet->mergeCells("A$cell:F$cell");
        $sheet->setCellValue("G$cell", 'TOTAL');
        $sheet->setCellValue("H$cell", $total);
        $sheet->getStyle("H$cell",)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle("A$cell:F$cell")->applyFromArray($styleArray);
        $sheet->getStyle("G$cell")->applyFromArray($styleArray);
        $sheet->getStyle("H$cell")->applyFromArray($styleArray);
        $sheet->getStyle("I$cell")->applyFromArray($styleArray);
        $sheet->getStyle("J$cell")->applyFromArray($styleArray);
        $sheet->getStyle("K$cell")->applyFromArray($styleArray);


        $sheet->setTitle("Reporte envios");

        // Crear tu archivo Office 2007 Excel (XLSX Formato)
        $writer = new Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'reporte_envios.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);

        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/reporte_envio_nacional_excel', name: 'app_reporte_envio_nacional_excel')]
    public function excelEnvioNacional(Request $request, EntityManagerInterface $entityManager): Response
    {
        $spreadsheet = new Spreadsheet();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('assets/images/facturas/logo.jpg');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(100);

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $drawing->setWorksheet($sheet);
        $sheet->getCell('A4')->setValue("#");
        $sheet->getCell('B4')->setValue("FECHA");

        $sheet->getCell('C4')->setValue("REMISIÓN");
        $sheet->getCell('D4')->setValue("GUIA");
        $sheet->getCell('E4')->setValue("REMITENTE");    
        $sheet->getCell('F4')->setValue("DESTINO");
        $sheet->getCell('G4')->setValue("DESTINATARIO");
        $sheet->getCell('H4')->setValue("DICE CONTENER");
        $sheet->getCell('I4')->setValue("UNIDADES");
        $sheet->getCell('J4')->setValue("PESO ");
        $sheet->getCell('K4')->setValue("VALOR DECLARADO");
        
        $sheet->getCell('L4')->setValue("VALOR \n TOTAL");
        $sheet->getCell('M4')->setValue("COSTO");
        $sheet->getCell('N4')->setValue("FORMA PAGO");
        $sheet->getCell('O4')->setValue("FACTURA");
        $sheet->getCell('P4')->setValue("RECIBO");
        $sheet->getCell('Q4')->setValue("USUARIO");
       

        $styleArray = array(
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => array('argb' => '070707'),
                ),
            ),
        );
        foreach (range('A', 'Q') as $columnID) {

            $sheet->getStyle($columnID . '4')->applyFromArray($styleArray);
        }

        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(10);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->getRowDimension('4')->setRowHeight(45, 'pt');
        $sheet->getRowDimension('1')->setRowHeight(100, 'px');
       






        $query = $entityManager->getRepository(EnviosNacionales::class)->createQueryBuilder('e')
            ->innerJoin(Clientes::class, 'c', Join::WITH,   'c.id = e.cliente')
            ->innerJoin(EnviosNacionalesUnidades::class, 'enu', Join::WITH,  'e.id = enu.envioNacional')
            ->andWhere('e.fecha >= :val')
            ->setParameter('val', $request->get('fecha_inicio'))
            ->andWhere('e.fecha <= :val1')
            ->setParameter('val1', $request->get('fecha_fin'));

        if ($request->get('quien_envia')) {

            $query->andWhere(' e.cliente = :val2')
                ->setParameter('val2', $request->get('quien_envia'));
        }
        if ($request->get('descripcion')) {

            $shearch = '%' . $request->get('descripcion') . '%';
            $query->andWhere(' e.descripcion like :val4')
                ->setParameter('val4', $shearch);
        }
        if ($request->get('facturado')) {
            if($request->get('facturado')=='facturado'){
                $query->andWhere(' e.facturado = :val3')
                ->setParameter('val3', 1);
            }else{
                $query->andWhere(' e.facturado != :val3')
                ->setParameter('val3', 1);
            }
           
        }


        $envios = $query->getQuery()->getResult();

        // Indice de la celda en la que se comienza a renderizar
        $cell = 5;
        $i = 0;
        $total = 0;

        foreach ($envios as $envio) {
            $i++;

            $sheet->setCellValue("A$cell", ($i));
            $sheet->setCellValue("B$cell", $envio->getFecha()->format('d-M-y'));
            $sheet->setCellValue("C$cell", $envio->getNumero());
            $items = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('fi')
            ->andWhere('fi.envioNacional = :val')
            ->setParameter('val', $envio->getId())
            ->getQuery()->getResult();
             $guias = '';
             if($envio->getNumeroGuia()){
                $guias = $envio->getNumeroGuia();
             }else {
                foreach ($items as $unidad){
                    $guias .= $unidad->getNumeroGuia()."\n";
                }
             }
            
            $sheet->setCellValue("D$cell", $guias);
            $sheet->setCellValue("E$cell", $envio->getCliente()->getRazonSocial());
            $sheet->setCellValue("F$cell", $envio->getMunicipioDestino()->getNombre().'('.$envio->getMunicipioDestino()->getDepartamento()->getNombre().')');
            $sheet->setCellValue("G$cell", $envio->getDestinatario());
            $sheet->setCellValue("H$cell", $envio->getDescripcion());
            $sheet->setCellValue("I$cell", $envio->getUnidades());
            $sheet->setCellValue("J$cell", $envio->getPeso());
            $sheet->setCellValue("K$cell", $envio->getSeguro());
            $sheet->setCellValue("L$cell", $envio->getValorTotal());
            $sheet->setCellValue("M$cell", 0);
            $sheet->setCellValue("N$cell", $envio->getFormaPago());
            if($envio->getFacturaItems()){
                $sheet->setCellValue("O$cell", $envio->getFacturaItems()->getFacturaClientes()->getFacturaResolucion()->getPrefijo().$envio->getFacturaItems()->getFacturaClientes()->getNumeroFactura());

            }
            if($envio->getReciboItems()){
                $sheet->setCellValue("P$cell"," RE -" . $envio->getReciboItems()->getReciboCaja()->getNumeroRecibo());
            }
            $sheet->getStyle("K$cell",)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            
             $sheet->getStyle("L$cell",)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

            if($envio->getCreador()){
                $sheet->setCellValue("Q$cell", $envio->getCreador()->__toString());
            }
            $sheet->getStyle("K$cell")->getAlignment()->setWrapText(true);
            $total += $envio->getValorTotal();
            foreach (range('A', 'Q') as $columnID) {

                $sheet->getStyle($columnID . $cell)->applyFromArray($styleArray);
            }

            // Continuar en una nueva fila
            $cell++;
        }
        $sheet->mergeCells("A$cell:F$cell");
        $sheet->setCellValue("G$cell", 'TOTAL');
        $sheet->setCellValue("H$cell", $total);
        $sheet->getStyle("H$cell",)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle("A$cell:F$cell")->applyFromArray($styleArray);
        $sheet->getStyle("G$cell")->applyFromArray($styleArray);
        $sheet->getStyle("H$cell")->applyFromArray($styleArray);
        $sheet->getStyle("I$cell")->applyFromArray($styleArray);
        $sheet->getStyle("J$cell")->applyFromArray($styleArray);
        $sheet->getStyle("K$cell")->applyFromArray($styleArray);
        $sheet->getStyle("L$cell")->applyFromArray($styleArray);
        $sheet->getStyle("M$cell")->applyFromArray($styleArray);
        $sheet->getStyle("N$cell")->applyFromArray($styleArray);
        $sheet->getStyle("O$cell")->applyFromArray($styleArray);
        $sheet->getStyle("P$cell")->applyFromArray($styleArray);
        $sheet->getStyle("Q$cell")->applyFromArray($styleArray);


        $sheet->setTitle("Reporte envios");

        // Crear tu archivo Office 2007 Excel (XLSX Formato)
        $writer = new Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'reporte_envios_nacionales.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);

        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
