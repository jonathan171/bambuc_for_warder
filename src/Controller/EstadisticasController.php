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

        $data = $envioRepository->getTotalesPorDia($fechaInicio, $fechaFin);

        $fechas = array_column($data, 'fecha');
        $totales = array_map('floatval', array_column($data, 'total'));
        $totalesFacturado = array_map('floatval', array_column($data, 'total_facturado'));
        $totalesRecibo = array_map('floatval', array_column($data, 'total_recibo'));

        return new JsonResponse([
            'labels' => $fechas,
            'totales' => $totales,
            'totalesFacturado' => $totalesFacturado,
            'totalesRecibo' => $totalesRecibo,
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

}
