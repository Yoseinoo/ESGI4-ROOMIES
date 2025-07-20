<?php
// src/Controller/GameController.php
namespace App\Controller;

use App\Entity\Room;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class GameController extends AbstractController {

    /**
     * Utilities pour le jeu
     */
    private GameService $game;

    public function __construct(GameService $game)
    {
        $this->game = $game;
    }
    
    /**
     * Permet de jouer un tour sur le jeu
     */
    #[Route('/rooms/{id}/play', methods: ['POST'])]
    public function play(Request $request, EntityManagerInterface $em, HubInterface $hub, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $row = $data['row'];
        $col = $data['col'];
        $playerId = $data['user'];

        $room = $em->getRepository(Room::class)->find($id);
        if (!$room) return new JsonResponse(['error' => 'Room not found'], 404);

        if ($room->getWinner() || $room->getCurrentTurn() !== $playerId) {
            return new JsonResponse(['error' => 'Not your turn or game over'], 403);
        }

        $board = $room->getBoard();

        // Check if cell is empty
        if ($board[$row][$col] !== '') {
            return new JsonResponse(['error' => 'Cell already occupied'], 400);
        }

        // Determine player's mark: X or O (assuming players[0] = X, players[1] = O)
        $players = $room->getPlayers();
        if (count($players) < 2) {
            return new JsonResponse(['error' => 'Not enough players'], 400);
        }

        $mark = $players[0] === $playerId ? 'X' : 'O';

        $board[$row][$col] = $mark;

        // Check for winner
        if ($this->game->checkWin($board, $mark)) {
            $room->setWinner($playerId);
        } else if ($this->game->isBoardFull($board)) {
            $room->setWinner('draw');  // special value to indicate draw
        }

        // Switch turn
        $nextPlayer = ($players[0] === $playerId) ? $players[1] : $players[0];
        $room->setCurrentTurn($nextPlayer);

        $room->setBoard($board);
        $em->flush();

        // Notify via Mercure
        $update = new Update("/rooms/{$id}", json_encode([
            'board' => $board,
            'currentTurn' => $room->getCurrentTurn(),
            'winner' => $room->getWinner(),
            'players' => $players,
        ]));
        $hub->publish($update);

        return new JsonResponse(['status' => 'ok']);
    }
}
