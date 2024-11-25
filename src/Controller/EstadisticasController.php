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

        $data = $envioRepository->getRangosDePesoConConteo($fechaInicio, $fechaFin, $paisDestino);

        $labels = [];
        $totales = [];
        $top3 = [];

        foreach ($data as $row) {
            $labels[] = $row['rango'];
            $totales[] = (int)$row['total'];

            // Procesar el top 3
            $pesos = explode(',', $row['pesos']);
            $conteoPesos = array_count_values($pesos);
            arsort($conteoPesos);

            $top3[$row['rango']] = array_slice($conteoPesos, 0, 3, true);
        }

        return new JsonResponse([
            'labels' => $labels,
            'data' => $totales,
            'top3' => $top3,
        ]);
    }
}
