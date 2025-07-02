<?php

namespace App\Controller\Api;

use App\Repository\EnvioRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/guias')]
class GuiasController extends AbstractController
{
    #[Route('/{numeroGuia}/validar', name: 'api_guias_validar', methods: ['GET'])]
    public function validarGuia(
        string $numeroGuia,
        EnvioRepository $envioRepository,
        Request $request,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        // ✅ Leer Authorization header
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(['error' => 'Missing or invalid Authorization header'], 401);
        }

        $token = trim(str_replace('Bearer ', '', $authHeader));

        try {
            // ✅ Verificar token usando JWTTokenManagerInterface
            $payload = $jwtManager->parse($token);

            // (Opcional) Verificar `exp` manualmente si quieres más control:
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return $this->json(['error' => 'Token expired'], 401);
            }

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Invalid token',
                'message' => $e->getMessage(),
            ], 401);
        }

        // ✅ Si el token es válido, hacer la lógica normal
        $envio = $envioRepository->findOneBy(['numeroEnvio' => $numeroGuia]);

        if (!$envio) {
            return $this->json(['existe' => false], 404);
        }

        return $this->json([
            'existe' => true,
            'numero_guia' => $envio->getNumeroEnvio(),
            'estado' => $envio->getEstado(),
            // Más campos si quieres
        ]);
    }
}
