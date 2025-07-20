<?php
// src/Controller/MercureTokenController.php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MercureTokenController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/mercure/token', name: 'mercure_token', methods: ['GET'])]
    public function getToken(): JsonResponse
    {
        // Payload personnalisé pour Mercure
        $payload = [
            'mercure' => [
                'subscribe' => ['chat'],  // autorise abonnement au topic 'chat'
                'publish' => ['chat'],    // autorise publication au topic 'chat' si besoin
            ],
        ];

        // Génère le token JWT à partir du payload
        $token = $this->jwtManager->createFromPayload($this->getUser() ?: null, $payload);

        return new JsonResponse(['token' => $token]);
    }
}
