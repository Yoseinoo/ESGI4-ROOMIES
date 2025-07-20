<?php
// src/Controller/RoomController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class RoomController extends AbstractController
{
    /**
     * Créé une room associé à l'utilisateur donné dans la requête POST
     */
    #[Route('/create-room', name: 'create_room', methods: ['POST'])]
    public function createRoom(Request $request, EntityManagerInterface $em): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate incoming data
        if (!isset($data['name'], $data['email'])) {
            return $this->json(['error' => 'Missing room name or email'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $room = new Room();
        $room->setName($data['name']);
        $room->setOwner($user);

        $em->persist($room);
        $em->flush();

        return $this->json([
            'id' => $room->getId(),
            'name' => $room->getName(),
            'owner' => $user->getEmail(),
        ]);
    }

    /**
     * Récupère toutes les rooms disponibles
     */
    #[Route('/rooms', name: 'get_rooms', methods: ['GET'])]
    public function getRooms(EntityManagerInterface $em): JsonResponse
    {
        $rooms = $em->getRepository(Room::class)->findAll();

        $data = array_map(fn(Room $room) => [
            'id' => $room->getId(),
            'name' => $room->getName(),
            'owner' => $room->getOwner()?->getEmail(),
        ], $rooms);

        return $this->json($data);
    }

    /**
     * Récupère les rooms de l'utilisateur
     */
    #[Route('/my-rooms', name: 'get_my_rooms', methods: ['POST'])]
    public function getRoomsByEmail(Request $request, EntityManagerInterface $em): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return $this->json(['error' => 'Missing email'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $rooms = $user->getRooms();

        $result = array_map(fn($room) => [
            'id' => $room->getId(),
            'name' => $room->getName(),
        ], $rooms->toArray());

        return $this->json($result);
    }

    //Supprime la room avec l'ID joint pour l'utilisateur donné dans la requête
    #[Route('/rooms/{id}', name: 'delete_room_by_email', methods: ['DELETE'])]
    public function deleteRoomByEmail(int $id, Request $request, EntityManagerInterface $em): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return $this->json(['error' => 'Missing email'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $room = $em->getRepository(Room::class)->find($id);
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        if ($room->getOwner() !== $user) {
            return $this->json(['error' => 'You do not own this room'], 403);
        }

        $em->remove($room);
        $em->flush();

        return $this->json(['success' => true]);
    }

    /**
     * Permet de mettre à jour la room et le game state quand un joueur rejoins
     */
    #[Route('/rooms/{id}/join', name: 'join_room', methods: ['POST'])]
    public function joinRoom(Request $request, Room $room, EntityManagerInterface $em, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json(['error' => 'Missing email'], 400);
        }

        if ($room->getPlayerCount() >= $room->getCapacity()) {
            return $this->json(['error' => 'Room is full'], 400);
        }

        if (!in_array($email, $room->getPlayers()) && $room->getPlayerCount() < $room->getCapacity()) {
            $players = $room->getPlayers();
            $players[] = $email;
            $room->setPlayers($players);
            $room->incrementPlayerCount();
        }

        if (!$room->getCurrentTurn()) {
            $room->setCurrentTurn($room->getPlayers()[0] ?? null);
        }
        
        $em->flush();

        $update = new Update("/rooms/{$room->getId()}", json_encode([
            'board' => $room->getBoard(),
            'currentTurn' => $room->getCurrentTurn(),
            'winner' => $room->getWinner(),
            'playerCount' => $room->getPlayerCount()
        ]));
        $hub->publish($update);

        return new JsonResponse(['status' => 'joined room']);
    }

    /**
     * Permet de mettre à jour la room et le game state quand un joueur quitte
     */
    #[Route('/rooms/{id}/leave', name: 'leave_room', methods: ['POST'])]
    public function leaveRoom(Request $request, Room $room, EntityManagerInterface $em, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return $this->json(['error' => 'Missing email'], 400);
        }

        if ($room->getCurrentTurn() == $email) {
            $room->setCurrentTurn(null);
        }
        $room->decrementPlayerCount();
        $em->flush();

        $update = new Update("/rooms/{$room->getId()}", json_encode([
            'board' => $room->getBoard(),
            'currentTurn' => $room->getCurrentTurn(),
            'winner' => $room->getWinner(),
            'playerCount' => $room->getPlayerCount()
        ]));
        $hub->publish($update);

        return $this->json("{'message' => 'Successfully left room'}");
    }
}
