<?php

include(__DIR__ . '/../vendor/autoload.php');

use Podo\GameOfLife\GameOfLife;
use Podo\GameOfLife\Decorators\JsonDecorator;

$width = 3;
$depth = 3;

$game = new GameOfLife($width, $depth);
$game->setCell(1, 1, true);
$game->setCell(1, 2, true);
$game->decorator(new JsonDecorator);

echo $game->render();
