<?php

namespace App\Controller\Api;

use App\Repository\EnvioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/guias')]
class GuiasController extends AbstractController
{
    #[Route('/{numeroGuia}/validar', name: 'api_guias_validar', methods: ['GET'])]
    public function validarGuia(string $numeroGuia, EnvioRepository $envioRepository): JsonResponse
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $envio = $envioRepository->findOneBy(['numeroGuia' => $numeroGuia]);

        if (!$envio) {
            return $this->json(['existe' => false], 404);
        }

        return $this->json([
            'existe' => true,
            'numero_guia' => $envio->getNumeroEnvio(),
            'estado' => $envio->getEstado(),
            // Puedes exponer más datos aquí si lo necesitas
        ]);
    }
}
