<?php

namespace Podo\GameOfLife\Decorators;

class HtmlDecorator extends DecoratorAbstract
{
    public $outputAlive = 'X';
    public $outputDead = '_';

    public function render()
    {
        $grid = $this->game->grid;
        $output = '<table class="table table-bordered">';
        $format = '<td>%s</td>';

        for ($y = 1; $y <= count($grid); $y++) {
            $line = '<tr>';
            for ($x = 1; $x <= count($grid[$y]); $x++) {
                $alive = $grid[$y][$x];
                $line .= sprintf(
                    $format,
                    ($alive ? $this->outputAlive : $this->outputDead)
                );
            }
            $output .= $line . '</tr>';
        }

        $output .= '</table>';
        return $output;
    }
}
