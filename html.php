<?php

include('vendor/autoload.php');

use Podo\GameOfLife\GameOfLife;
use Podo\GameOfLife\Decorators\HtmlDecorator;

$width = 3;
$depth = 3;

$game = new GameOfLife($width, $depth);
$game->decorator(new HtmlDecorator);

echo $game->render();
