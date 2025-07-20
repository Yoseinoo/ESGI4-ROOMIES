<?php
// src/Entity/Room.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Room
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $capacity = 2;

    #[ORM\Column(type: 'integer')]
    private int $playerCount = 0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * GAME STATE
     */
    #[ORM\Column(type: 'json')]
    private array $board = []; // 6x7 grid, default empty

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $currentTurn = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $winner = null;

    #[ORM\Column(type: 'json')]
    private array $players = []; // [player1_id, player2_id]

    public function __construct()
    {
        // Initialize 3x3 empty board for Tic Tac Toe
        $this->board = array_fill(0, 3, array_fill(0, 3, ''));
        $this->players = [];
        $this->currentTurn = null;
        $this->winner = null;
        $this->playerCount = 0;
        $this->capacity = 2;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getPlayerCount(): int
    {
        return $this->playerCount;
    }

    public function setPlayerCount(int $count): self
    {
        $this->playerCount = $count;
        return $this;
    }

    public function incrementPlayerCount(): self
    {
        if ($this->playerCount < $this->capacity) {
            $this->playerCount++;
        }
        return $this;
    }

    public function decrementPlayerCount(): self
    {
        if ($this->playerCount > 0) {
            $this->playerCount--;
        }
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $user): self
    {
        $this->owner = $user;
        return $this;
    }

    /**
     * GAME STATE
     */
    public function getBoard(): array { return $this->board; }
    public function setBoard(array $board): self { $this->board = $board; return $this; }

    public function getCurrentTurn(): ?string { return $this->currentTurn; }
    public function setCurrentTurn(?string $turn): self { $this->currentTurn = $turn; return $this; }

    public function getWinner(): ?string { return $this->winner; }
    public function setWinner(?string $winner): self { $this->winner = $winner; return $this; }

    public function getPlayers(): array { return $this->players; }
    public function setPlayers(array $players): self { $this->players = $players; return $this; }
}
