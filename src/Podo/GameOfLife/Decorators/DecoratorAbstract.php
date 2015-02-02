<?php

namespace Podo\GameOfLife\Decorators;

use Podo\GameOfLife\GameOfLife;

abstract class DecoratorAbstract
{
    public $game;

    function setGame(GameOfLife $game)
    {
        $this->game = $game;
    }

    abstract function render();
}
