<?php

namespace App\Tests\Service;

use App\Service\GameService;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private GameService $game;

    protected function setUp(): void
    {
        $this->game = new GameService();
    }

    public function testWinByRow(): void
    {
        $board = [
            ['X', 'X', 'X'],
            ['', '', ''],
            ['', '', ''],
        ];
        $this->assertTrue($this->game->checkWin($board, 'X'));
    }

    public function testWinByColumn(): void
    {
        $board = [
            ['O', '', ''],
            ['O', '', ''],
            ['O', '', ''],
        ];
        $this->assertTrue($this->game->checkWin($board, 'O'));
    }

    public function testWinByDiagonal(): void
    {
        $board = [
            ['X', '', ''],
            ['', 'X', ''],
            ['', '', 'X'],
        ];
        $this->assertTrue($this->game->checkWin($board, 'X'));
    }

    public function testNoWinner(): void
    {
        $board = [
            ['X', 'O', 'X'],
            ['O', 'X', 'O'],
            ['O', 'X', 'O'],
        ];
        $this->assertFalse($this->game->checkWin($board, 'X'));
        $this->assertFalse($this->game->checkWin($board, 'O'));
    }

    public function testBoardFull(): void
    {
        $board = [
            ['X', 'O', 'X'],
            ['X', 'O', 'O'],
            ['O', 'X', 'X'],
        ];
        $this->assertTrue($this->game->isBoardFull($board));
    }

    public function testBoardNotFull(): void
    {
        $board = [
            ['X', '', 'X'],
            ['X', 'O', 'O'],
            ['O', 'X', 'X'],
        ];
        $this->assertFalse($this->game->isBoardFull($board));
    }
}
