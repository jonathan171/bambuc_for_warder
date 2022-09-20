<?php

namespace App\Controller;

use App\Entity\EnviosNacionales;
use App\Entity\EnviosNacionalesUnidades;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class MigracionController extends AbstractController
{   
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    #[Route('/migracion', name: 'app_migracion_exportar_envio')]
    public function index(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('migracion/index.html.twig', [
        ]);
    }

    #[Route('/exportar_envio_excel', name: 'app_migracion_exportar_envio_excel')]
    public function exportarEnvioExcel(Request $request, EntityManagerInterface $entityManager): Response
    {
        $spreadsheet = new Spreadsheet();

       

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue("NOMBRE DE DESTINATARIO");
        $sheet->getCell('B1')->setValue("DIRECCION");

        $sheet->getCell('C1')->setValue("CIUDAD");
        $sheet->getCell('D1')->setValue("TELEFONO");
        $sheet->getCell('E1')->setValue("PESO");
        $sheet->getCell('F1')->setValue("VALOR DECLARADO");
        $sheet->getCell('G1')->setValue("LARGO");
        $sheet->getCell('H1')->setValue("ALTO");
        $sheet->getCell('I1')->setValue("ANCHO");
        $sheet->getCell('J1')->setValue("REFERENCIA");
        $sheet->getCell('K1')->setValue("OBSERVACIONES");



       

        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(50);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(50);
        $sheet->getColumnDimension('G')->setWidth(50);
        $sheet->getColumnDimension('H')->setWidth(50);
        $sheet->getColumnDimension('I')->setWidth(50);
        $sheet->getColumnDimension('J')->setWidth(50);
        $sheet->getColumnDimension('K')->setWidth(50);
       // $sheet->getStyle('B4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
       

       






        $query = $entityManager->getRepository(EnviosNacionales::class)->createQueryBuilder('e')
            ->andWhere('e.fecha >= :val')
            ->setParameter('val', $request->get('fecha_inicio'))
            ->andWhere('e.fecha <= :val1')
            ->setParameter('val1', $request->get('fecha_fin'));

        if ($request->get('quien_envia')) {

           
            $query->andWhere(' e.cliente = :val2')
                ->setParameter('val2', $request->get('quien_envia'));
        }


        $envios = $query->getQuery()->getResult();

        // Indice de la celda en la que se comienza a renderizar
        $cell = 2;
        $i = 0;
        $total = 0;
        

        foreach ($envios as $envio) {

            $unidades = $entityManager->getRepository(EnviosNacionalesUnidades::class)->createQueryBuilder('eu')
            ->andWhere('eu.envioNacional = :val')
            ->setParameter('val', $envio->getId())
            ->getQuery()->getResult();

            foreach($unidades as $unidad){
                
            $sheet->setCellValue("A$cell", ($envio->getDestinatario()));
            $sheet->setCellValue("B$cell", $envio->getDireccionDestino());
            $sheet->setCellValue("C$cell", mb_strtoupper($envio->getMunicipioDestino()->getNombre().'-'.$envio->getMunicipioDestino()->getDepartamento()->getNombre()));
            $sheet->setCellValue("D$cell", $envio->getTelefonoDestinatario());
            $sheet->setCellValue("E$cell", $unidad->getPeso());
            $sheet->setCellValue("F$cell", $unidad->getValorDeclarado());
            $sheet->setCellValue("G$cell", $unidad->getLargo());
            $sheet->setCellValue("H$cell", $unidad->getAlto());
            $sheet->setCellValue("I$cell", $unidad->getAncho());
            $sheet->setCellValue("J$cell", $envio->getCliente()->getRazonSocial().'['.$unidad->getNumeroReferencia().']');
            $sheet->setCellValue("K$cell", 'LLAMAR AL REMITENTE ANTES DE REALIZAR LA DEVOLUCION');

           

            // Continuar en una nueva fila
            $cell++;

            }
           

        }
       



        $sheet->setTitle("Exportacion Envios 472");

        // Crear tu archivo Office 2007 Excel (XLSX Formato)
        $writer = new Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'envios_472.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);

        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/importar_472', name: 'app_migracion_importar_472')]
    public function importar472(): Response
    {   
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('migracion/importar_472.html.twig', [
        ]);
    }

    #[Route('/importar_excel_472', name: 'app_migracion_importar_excel_472')]
    public function importarExcel472(Request $request, EntityManagerInterface $entityManager): Response
    {   
       
        if ($request->getMethod() == "POST") {
            $file = $request->files->has('archivo_excel') ? $request->files->get('archivo_excel') : null;
            if (!$file) {
                $errors[] = 'Missing File';
            }
            
        
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());

            // Need this otherwise dates and such are returned formatted
            /** @noinspection PhpUndefinedMethodInspection */
            $reader->setReadDataOnly(true);
    
            // Just grab all the rows
            $wb = $reader->load($file->getRealPath());
            $ws = $wb->getSheet(0);
            $rows = $ws->toArray();
            $i=0;
            $this->result = array();
            foreach($rows as $row) {
                if($i!=0){
                    $_log = $this->processEnvioExcelRow($row);

                    if ($_log) {
                        $this->result[] = $_log;
                    }
                    
                }
                $i++;
                // this is where you do your database stuff
               
            }
            $this->addFlash(
                'success',
                'Archivo de excel subido y procesado satisfactoriamente'
            );


        }else{
            $this->addFlash(
                'error',
                'El archivo de excel no pudo ser subido/procesado, por favor intente nuevamente'
            );
        }
        $this->addFlash(
            'result',
             $this->result
        );
        return $this->redirectToRoute('app_migracion_importar_472', [], Response::HTTP_SEE_OTHER);

    }
    
     /**
     * Procesa cada fila del archivo excel subido.
     * 
     * @param Array $excel_row
     * @return boolean
     */
    private function processEnvioExcelRow($excel_row) {

        /* @var $grupoAutorizacion GrupoAutorizacion */
        $_log = array(
            "status" => "success",
            "messages" => array(),
            "data" => array()
        );
      
        $_rowData = array(
            "guia_master" => $excel_row[0],
            "numero_guia" => $excel_row[1],
            "destinatario" => $excel_row[2],
            "direccion_destinatario" => trim($excel_row[3]),
            "ciudad" => $excel_row[4],
            "pais" => $excel_row[5],
            "valor_declarado" => $excel_row[6],
            "valor_por_recaudar" => $excel_row[7],
            "peso_fisico" => $excel_row[8],
            "largo" => $excel_row[9],
            "ancho" => $excel_row[10],
            "alto" => $excel_row[11],
            "peso_volumetrico" => $excel_row[12],
            "peso_tarificado" => $excel_row[13],
            "tasa_manejo" => $excel_row[14],
            "costo_manejo" => $excel_row[15],
            "valor_total" => $excel_row[16],
            "descuento_adicional" => $excel_row[17],
            "descuento_servicio" => $excel_row[18],
            "contenido" => $excel_row[19],
            "referencia" => $excel_row[20],
            "observaciones" => $excel_row[21],
            "locker" => $excel_row[22],
            "casillero_destino" => $excel_row[23],
            "tamano" => $excel_row[24],
            "telefono" => $excel_row[25],
            "email" => $excel_row[26],
            "codigo_postal" => $excel_row[27],
            "codigo_operativo" => $excel_row[28],
            "cobertura" => $excel_row[29],
            "causa_rechazo" => $excel_row[30],
        );

        $_log["excel_data"] = $_rowData;

        // Si todos los elementos de la fila estan vacíos, saltelo ...
        if (!array_filter($excel_row)) {
            return false;
        }
      
        // 1. Validadores previos a inserción de datos
        if (!$_rowData["numero_guia"] ) {
            $_log["status"] = "error";
            $_log["messages"][] = "No se puede subir informacion sin un numero de Guía.";

            return $_log;
        }

        $pos1 = strpos($_rowData["referencia"], '[');
        $pos2 = strpos($_rowData["referencia"], ']');

        if($pos1 && $pos2){
            $leng = $pos2-$pos1;
            $numero_referencia =  substr($_rowData["referencia"],$pos1+1,$leng-1);
        }else{
            $_log["status"] = "error";
            $_log["messages"][] = "No se puede subir informacion sin un numero de referencia.";

            return $_log;

        }
        
        $unidad = $this->manager->getRepository(EnviosNacionalesUnidades::class)->findOneBy(['numeroReferencia'=>$numero_referencia]);
        
        if($unidad){
            $unidad->setPeso($_rowData['peso_fisico']);
            $unidad->setValorDeclarado($_rowData['valor_declarado']);
            $unidad->setLargo($_rowData['largo']);
            $unidad->setAlto($_rowData['alto']);
            $unidad->setAncho($_rowData['ancho']);
            $unidad->setNumeroGuia($_rowData['numero_guia']);
            $this->manager->persist($unidad);
            $this->manager->flush();
           
        }else{
            $_log["status"] = "error";
            $_log["messages"][] = "No se encontro ningun envio con este  número de referencia.";

            return $_log;
        }

        
        return $_log;
    }


}
