<?php

namespace App\Service;

class GameService
{
    // Utility to check win condition
    public function checkWin(array $board, string $mark): bool
    {
        // Check rows and columns
        for ($i = 0; $i < 3; $i++) {
            if (
                ($board[$i][0] === $mark && $board[$i][1] === $mark && $board[$i][2] === $mark) ||
                ($board[0][$i] === $mark && $board[1][$i] === $mark && $board[2][$i] === $mark)
            ) {
                return true;
            }
        }

        // Check diagonals
        if (
            ($board[0][0] === $mark && $board[1][1] === $mark && $board[2][2] === $mark) ||
            ($board[0][2] === $mark && $board[1][1] === $mark && $board[2][0] === $mark)
        ) {
            return true;
        }

        return false;
    }

    public function isBoardFull(array $board): bool
    {
        foreach ($board as $row) {
            foreach ($row as $cell) {
                if ($cell === '') return false;
            }
        }
        return true;
    }
}