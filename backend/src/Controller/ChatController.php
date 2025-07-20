<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    /**
     * Route utilisée pour envoyer un message dans le websocket
     */
    #[Route('/chat/send', name: 'chat_send', methods: ['POST'])]
    public function send(Request $request, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';

        if (!$message) {
            return new JsonResponse(['error' => 'Message is empty'], 400);
        }

        // Publier le message sur le topic "chat"
        $update = new Update(
            'chat/general', // topic
            json_encode(['message' => $message, 'timestamp' => time()])
        );

        $hub->publish($update);

        return new JsonResponse(['status' => 'Message sent']);
    }
}
