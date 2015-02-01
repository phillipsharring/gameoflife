<?php namespace Podo;

use \InvalidArgumentException;

class GameOfLife {

    public $width;
    public $depth;

    public $grid = [];
    public $nextGeneration = [];

    public $generation = 1;
    public $output_alive = '█';
    public $output_dead = '░';

    public function __construct($width, $depth)
    {
        $this->width = $width;
        $this->depth = $depth;
        $this->initGrid();
    }

    public function initGrid()
    {
        for ($y = 1; $y <= $this->depth; $y++) {
            for ($x = 1; $x <= $this->width; $x++) {
                $this->grid[$y][$x] = false;
            }
        }
    }

    function grid()
    {
        return $this->grid;
    }

    function generation()
    {
        return $this->generation;
    }

    function getCell($x, $y)
    {
        $this->guardCoordinates($x, $y);
        return $this->grid[$y][$x];
    }

    function setCell($x, $y, $result)
    {
        $this->guardCoordinates($x, $y);
        $this->grid[$y][$x] = $result;
    }

    function createCell($x, $y)
    {
        $this->guardCoordinates($x, $y);
        $this->nextGeneration[$y][$x] = true;
    }

    function killCell($x, $y)
    {
        $this->guardCoordinates($x, $y);
        $this->nextGeneration[$y][$x] = false;
    }

    function getNeighborHood($x, $y)
    {
        $neighborhood = ['alive' => 0, 'dead' => 0];

        $x0 = $x-1;
        $y0 = $y-1;
        $x2 = $x0+2;
        $y2 = $y0+2;

        $grid = $this->grid();

        for ($i = max($y0, 1); $i <= min($y2, $this->depth); $i++) {
            for ($j = max($x0, 1); $j <= min($x2, $this->width); $j++) {
                if ($i == $y && $j == $x) {
                    continue;
                }
                $key = ($grid[$i][$j]) ? 'alive' : 'dead';
                $neighborhood[$key] += 1;
            }
        }

        $count = $neighborhood['alive'] + $neighborhood['dead'];
        $fill = 8 - $count;
        $neighborhood['dead'] += $fill;

        return $neighborhood;
    }

    function age($generations)
    {
        for ($i = 0; $i < $generations; $i++) {

            for ($y = 1; $y <= $this->depth; $y++) {
                for ($x = 1; $x <= $this->width; $x++) {
                    if ($this->evaluate($x, $y)) {
                        $this->createCell($x, $y);
                    } else {
                        $this->killCell($x, $y);
                    }
                }
            }

            $this->grid = $this->nextGeneration;
            $this->generation += 1;
        }

        return $this->generation();
    }

    function evaluate($x, $y)
    {
        $result = null;
        $neighborhood = $this->getNeighborHood($x, $y);
        $cell = $this->getCell($x, $y);

        if ($cell && $this->cellIsLonely($neighborhood)) {
            $result = false;
        }

        elseif($cell && $this->cellHasEnoughNeighbors($neighborhood)) {
            $result = true;
        }

        elseif ($cell && $this->cellIsOverCrowded($neighborhood)) {
            $result = false;
        }

        elseif (!$cell && $this->cellHasThreeNeighbors($neighborhood)) {
            $result = true;
        }

        if ($result) {
            $this->createCell($x, $y);
        } else {
            $this->killCell($x, $y);
        }

        return $result;
    }

    public function render()
    {
        $grid = $this->grid();
        $output = PHP_EOL;

        for ($y = 1; $y <= count($grid); $y++) {
            $line = '';
            for ($x = 1; $x <= count($grid[$y]); $x++) {
                $cell = $grid[$y][$x];
                $line .= ($cell ? $this->output_alive : $this->output_dead);
            }
            $output .= $line . PHP_EOL;
        }

        return $output;
    }

    private function cellIsLonely($neighborhood)
    {
        return ($neighborhood['alive'] < 2);
    }

    private function cellHasEnoughNeighbors($neighborhood)
    {
        return ($neighborhood['alive'] == 2 || $neighborhood['alive'] == 3);
    }

    private function cellIsOverCrowded($neighborhood)
    {
        return ($neighborhood['alive'] > 3);
    }

    private function cellHasThreeNeighbors($neighborhood)
    {
        return ($neighborhood['alive'] == 3);
    }

    private function guardCoordinates($x, $y)
    {
        $grid = $this->grid();
        $depth = count($grid);
        $width = count($grid[1]);

        if ($x > $width) {
            throw new InvalidArgumentException("X: $x is beyond width: $width");
        }

        if ($y > $depth) {
            throw new InvalidArgumentException("Y: $y is beyond depth: $depth");
        }
    }
}
