<?php

namespace Podo;

use \InvalidArgumentException;

/**
 * Class GameOfLife
 * @package Podo
 */
class GameOfLife
{
    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $depth;

    /**
     * @var array
     */
    public $grid = [];

    /**
     * @var array
     */
    public $nextGeneration = [];

    /**
     * @var int
     */
    public $generation = 1;

    /**
     * @var string
     */
    public $outputAlive = '█';

    /**
     * @var string
     */
    public $outputDead = '░';

    /**
     * Construct
     *
     * @param int $width
     * @param int $depth
     */
    function __construct($width, $depth)
    {
        $this->width = $width;
        $this->depth = $depth;
        $this->initGrid();
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    function initGrid()
    {
        for ($y = 1; $y <= $this->depth; $y++) {
            for ($x = 1; $x <= $this->width; $x++) {
                $this->grid[$y][$x] = false;
            }
        }
    }

    /**
     * Get a Cell
     *
     * @param int $x
     * @param int $y
     *
     * @return mixed
     */
    function getCell($x, $y)
    {
        $this->guardCoordinates($x, $y);
        return $this->grid[$y][$x];
    }

    /**
     * Set the value of a Cell (in the current generation)
     *
     * @param int $x
     * @param int $y
     * @param bool $alive
     *
     * @return void
     */
    function setCell($x, $y, $alive)
    {
        $this->guardCoordinates($x, $y);
        $this->grid[$y][$x] = $alive;
    }

    /**
     * Dispatch cell in the next generation
     *
     * @param int $x
     * @param int $y
     * @param bool $live
     *
     * @return void
     */
    function dispatchCell($x, $y, $live)
    {
        $this->guardCoordinates($x, $y);
        $this->nextGeneration[$y][$x] = $live;
    }

    /**
     * Get the neighborhood for a cell
     *
     * This simply returns an array with keys 'alive' and 'dead'
     * which are counts of the status of the cells surrounding a given cell.
     *
     * It doesn't matter where they are, just how many there are.
     *
     * This assumes that cells off the edge of the grid are dead; not sure if that's right.
     *
     * @param int $x
     * @param int $y
     *
     * @return array
     */
    function getNeighborHood($x, $y)
    {
        $neighborhood = ['alive' => 0, 'dead' => 0];

        // back and forward along width
        $x0 = $x-1;
        $y0 = $y-1;

        // back and forward along depth
        $x2 = $x0+2;
        $y2 = $y0+2;

        $grid = $this->grid;

        // don't go off the edge of the grid
        for ($i = max($y0, 1); $i <= min($y2, $this->depth); $i++) {
            for ($j = max($x0, 1); $j <= min($x2, $this->width); $j++) {
                // ignore the current cell
                if ($i == $y && $j == $x) {
                    continue;
                }
                $key = ($grid[$i][$j]) ? 'alive' : 'dead';
                $neighborhood[$key] += 1;
            }
        }

        $count = $neighborhood['alive'] + $neighborhood['dead'];

        // fill in cells off the grid as dead
        $fill = 8 - $count;
        $neighborhood['dead'] += $fill;

        return $neighborhood;
    }

    /**
     * Age the grid
     *
     * @param int $generations how many generations to age
     *
     * @return int the current generation
     */
    function age($generations)
    {
        for ($i = 0; $i < $generations; $i++) {

            for ($y = 1; $y <= $this->depth; $y++) {
                for ($x = 1; $x <= $this->width; $x++) {
                    // we should throw if this is null, maybe
                    $live = $this->evaluate($x, $y);
                    $this->dispatchCell($x, $y, $live);
                }
            }

            $this->grid = $this->nextGeneration;
            $this->generation += 1;
        }

        return $this->generation;
    }

    /**
     * Evaluate
     *
     * Evaluate a cell and decide if it lives or dies in the next generation
     *
     * @param int $x
     * @param int $y
     *
     * @return bool|null
     */
    function evaluate($x, $y)
    {
        $live = null;

        $neighborhood = $this->getNeighborHood($x, $y);
        $alive = $this->getCell($x, $y);

        if ($this->cellIsLonely($alive, $neighborhood)) {
            $live = false;
        }

        elseif($this->cellHasEnoughNeighbors($alive, $neighborhood)) {
            $live = true;
        }

        elseif ($this->cellIsOverCrowded($alive, $neighborhood)) {
            $live = false;
        }

        elseif ($this->cellHasThreeNeighbors($alive, $neighborhood)) {
            $live = true;
        }

        $this->dispatchCell($x, $y, $live);

        return $live;
    }

    /**
     * Render
     *
     * Draw the grid in its current generation
     *
     * @return string
     */
    function render()
    {
        $grid = $this->grid;
        $output = PHP_EOL;

        for ($y = 1; $y <= count($grid); $y++) {
            $line = '';
            for ($x = 1; $x <= count($grid[$y]); $x++) {
                $cell = $grid[$y][$x];
                $line .= ($cell ? $this->outputAlive : $this->outputDead);
            }
            $output .= $line . PHP_EOL;
        }

        return $output;
    }

    /**
     * Cell is Lonely
     *
     * "Any live cell with fewer than two live neighbours dies, as if caused by under-population."
     *
     * @param bool $alive
     * @param array $neighborhood
     *
     * @return bool
     */
    private function cellIsLonely($alive, array $neighborhood)
    {
        return ($alive && $neighborhood['alive'] < 2);
    }

    /**
     * Cell has enough neighbors
     *
     * "Any live cell with two or three live neighbours lives on to the next generation."
     *
     * Is this function even necessary, since it doesn't really change the cell?
     *
     * @param bool $alive
     * @param array $neighborhood
     *
     * @return bool
     */
    private function cellHasEnoughNeighbors($alive, array $neighborhood)
    {
        return ($alive && $neighborhood['alive'] == 2 || $neighborhood['alive'] == 3);
    }

    /**
     * Cell is Over Crowded
     *
     * "Any live cell with more than three live neighbours dies, as if by overcrowding."
     *
     * @param bool $alive
     * @param array $neighborhood
     *
     * @return bool
     */
    private function cellIsOverCrowded($alive, array $neighborhood)
    {
        return ($alive && $neighborhood['alive'] > 3);
    }

    /**
     * Cell has Three Neighbors
     *
     * "Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction."
     *
     * @param bool $alive
     * @param array $neighborhood
     *
     * @return bool
     */
    private function cellHasThreeNeighbors($alive, array $neighborhood)
    {
        return (!$alive && $neighborhood['alive'] == 3);
    }

    /**
     * Guard coordinates
     *
     * This throws if coordinates off the grid are accessed.
     *
     * @param int $x
     * @param int $y
     *
     * @throws \InvalidArgumentException
     */
    private function guardCoordinates($x, $y)
    {
        $grid = $this->grid;
        $depth = count($grid);
        $width = count($grid[1]);

        if ($x < 1) {
            throw new InvalidArgumentException("X: $x is less than 1");
        }

        if ($y < 1) {
            throw new InvalidArgumentException("Y: $x is less than 1");
        }

        if ($x > $width) {
            throw new InvalidArgumentException("X: $x is beyond width: $width");
        }

        if ($y > $depth) {
            throw new InvalidArgumentException("Y: $y is beyond depth: $depth");
        }
    }
}
