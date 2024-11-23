<?php

namespace App\Controller;

use App\Repository\EnvioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstadisticasController extends AbstractController
{
    #[Route('/estadisticas', name: 'app_estadisticas')]
    public function index(): Response
    {
        return $this->render('estadisticas/index.html.twig', []);
    }
    #[Route('/estadisticas_envios_por_pais', name: 'app_estadisticas_envios_por_pais')]
    public function enviosPorPais(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        
        $data = $envioRepository->getEnviosPorPaisOrigen($fechaInicio, $fechaFin);
    
        $labels = array_column($data, 'municipio');
        $totals = array_column($data, 'total');
        $totalesCobrar = array_column($data, 'total_cobrar');
    
        return new JsonResponse([
            'labels' => $labels,
            'totales' => $totals,
            'totalesCobrar' => $totalesCobrar,
        ]);
    }
    #[Route('/estadisticas_envios_por_pais_destino', name: 'app_estadisticas_envios_por_pais_destino')]
    public function enviosPorPaisDestino(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        
        $data = $envioRepository->getEnviosPorPaisDestino($fechaInicio, $fechaFin);

        $labels = array_column($data, 'pais');
        $totals = array_column($data, 'total');
        $totalesCobrar = array_column($data, 'total_cobrar');

        return new JsonResponse([
            'labels' => $labels,
            'totales' => $totals,
            'totalesCobrar' => $totalesCobrar,
        ]);
    }

    #[Route('/estadisticas_peso_total_por_dia', name: 'app_estadisticas_peso_total_por_dia')]
    public function totalesPorDia(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio') ?: (new \DateTime())->modify('-3 months')->format('Y-m-d');
        $fechaFin = $request->query->get('fechaFin') ?: (new \DateTime())->format('Y-m-d');
        $agrupacion = $request->query->get('agrupacion') ?: 'daily';

        $data = $envioRepository->getTotalesPorDia($fechaInicio, $fechaFin, $agrupacion);

        $fechas = array_column($data, 'fecha');
        $totales = array_map('floatval', array_column($data, 'total'));
        $totalesFacturado = array_map('floatval', array_column($data, 'total_facturado'));
        $totalesRecibo = array_map('floatval', array_column($data, 'total_recibo'));
        $totalesSinCobrar = array_map('floatval', array_column($data, 'total_sin_cobrar'));

        return new JsonResponse([
            'labels' => $fechas,
            'totales' => $totales,
            'totalesFacturado' => $totalesFacturado,
            'totalesRecibo' => $totalesRecibo,
            'totalesSinCobrar' => $totalesSinCobrar,
        ]);
    }

    #[Route('/estadisticas_envios_por_empresa', name: 'app_estadisticas_envios_por_empresa')]
    public function enviosPorEmpresa(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        
        $data = $envioRepository->getEnviosPorEmpresa($fechaInicio, $fechaFin);

        $labels = array_column($data, 'empresa');
        $totals = array_column($data, 'total');

        return new JsonResponse([
            'labels' => $labels,
            'data' => $totals,
        ]);
    }
    #[Route('/estadisticas_envios_rangos_de_peso', name: 'app_estadisticas_envios_rangos_de_peso')]
    public function rangosDePesoConTop3(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        $paisDestino = $request->query->get('paisDestino');

        $data = $envioRepository->getRangosDePesoConPesos($fechaInicio, $fechaFin, $paisDestino);

        $rangos = [
            'rango_0_5' => [],
            'rango_5_10' => [],
            'rango_10_20' => [],
            'rango_20_30' => [],
            'rango_30_40' => [],
            'rango_40_50' => [],
            'rango_mas_50' => []
        ];

        foreach ($data as $row) {
            $peso = round((float)$row['pesos'], 1); // Redondear a 1 decimal

            if ($peso <= 5) {
                $rangos['rango_0_5'][] = $peso;
            } elseif ($peso > 5 && $peso <= 10) {
                $rangos['rango_5_10'][] = $peso;
            } elseif ($peso > 10 && $peso <= 20) {
                $rangos['rango_10_20'][] = $peso;
            } elseif ($peso > 20 && $peso <= 30) {
                $rangos['rango_20_30'][] = $peso;
            } elseif ($peso > 30 && $peso <= 40) {
                $rangos['rango_30_40'][] = $peso;
            } elseif ($peso > 40 && $peso <= 50) {
                $rangos['rango_40_50'][] = $peso;
            } else {
                $rangos['rango_mas_50'][] = $peso;
            }
        }

        $top3 = [];
        foreach ($rangos as $rango => $pesos) {
            // Contar ocurrencias de valores redondeados
            $conteo = array_count_values($pesos);
            arsort($conteo);

            // Obtener los 3 más frecuentes
            $top3[$rango] = array_slice($conteo, 0, 3, true);
        }

        return new JsonResponse([
            'labels' => [
                '0 <= 5',
                '5 <= 10',
                '10 <= 20',
                '20 <= 30',
                '30 <= 40',
                '40 <= 50',
                'Más de 50'
            ],
            'data' => [
                count($rangos['rango_0_5']),
                count($rangos['rango_5_10']),
                count($rangos['rango_10_20']),
                count($rangos['rango_20_30']),
                count($rangos['rango_30_40']),
                count($rangos['rango_40_50']),
                count($rangos['rango_mas_50'])
            ],
            'top3' => $top3
        ]);
    }
}
