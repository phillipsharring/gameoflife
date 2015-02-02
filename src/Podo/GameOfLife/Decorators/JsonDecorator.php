<?php

namespace Podo\GameOfLife\Decorators;

use \InvalidArgumentException;

class JsonDecorator extends DecoratorAbstract
{
    public function render()
    {
        $grid = $this->game->grid;
        $output = ['alive' => []];

        for ($y = 1; $y <= count($grid); $y++) {
            for ($x = 1; $x <= count($grid[$y]); $x++) {
                $alive = $grid[$y][$x];
                if ($alive) {
                    $output['alive'][] = [$x, $y];
                }
            }
        }

        return json_encode($output);
    }
}
