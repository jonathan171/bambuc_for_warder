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
    
        $labels = array_column($data, 'pais');
        $totals = array_column($data, 'total');
    
        return new JsonResponse([
            'labels' => $labels,
            'data' => $totals,
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

        return new JsonResponse([
            'labels' => $labels,
            'data' => $totals,
        ]);
    }

    #[Route('/estadisticas_peso_total_por_dia', name: 'app_estadisticas_peso_total_por_dia')]
    public function pesoTotalPorDia(Request $request, EnvioRepository $envioRepository): JsonResponse
    {
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');
        
        $data = $envioRepository->getPesoTotalPorDia($fechaInicio, $fechaFin);

        $fechas = array_column($data, 'fecha');
        $totalesPeso = array_map('floatval', array_column($data, 'total_peso'));

        return new JsonResponse([
            'labels' => $fechas,
            'data' => $totalesPeso,
        ]);
    }

}
