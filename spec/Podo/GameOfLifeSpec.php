<?php

namespace spec\Podo;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Exception\Prediction\FailedPredictionException;

class GameOfLifeSpec extends ObjectBehavior
{
    public $width = 6;
    public $height = 6;

    function let()
    {
        $this->beConstructedWith($this->width, $this->height);
    }

    function it_should_have_a_grid_set_to_width_and_height()
    {
        $this->grid->shouldHaveCount($this->height);
        $this->grid->shouldHaveKey($this->height);
    }

    function it_should_age_one_generation()
    {
        $this->age(1);
        $this->generation->shouldReturn(2);
    }

    function it_should_guard_coordinates()
    {
        $this->shouldThrow('\InvalidArgumentException')->during(
            'getCell',
            [$this->width + 1, $this->height + 1]
        );
    }

    function it_should_create_a_cell()
    {
        $live = true;
        $this->dispatchCell(1, 1, $live);
        $this->nextGrid[1][1]->shouldReturn($live);
    }

    function it_should_kill_a_cell()
    {
        $live = false;
        $this->dispatchCell(1, 1, $live);
        $this->nextGrid[1][1]->shouldReturn($live);
    }

    function it_should_get_the_neighborhood_for_a_cell()
    {
        $x = $y = 2;

        $result = $this->getNeighborHood($x, $y);
        $neighborhood = $result->getWrappedObject();

        $result->shouldHaveKey('alive');
        $result->shouldHaveKey('dead');
        $result->shouldHaveCount(2);

        $expectedCount = 8;
        $count = $neighborhood['alive'] + $neighborhood['dead'];

        if ($count != $expectedCount) {
            throw new FailedPredictionException(
                "Neighborhood count: {$count} must be "
                . "expected count: {$expectedCount}"
            );
        }
    }

    function it_should_evaluate_the_whole_grid()
    {
        $this->evaluateGrid();
    }

    function it_should_render()
    {
        $this->setCell(1, 1, true);
        $this->render();
    }

    function it_should_kill_a_lonely_cell()
    {
        $x = $y = 2;
        $this->setCell($x, $y, true);
        $this->evaluateCell($x, $y)->shouldReturn(false);
    }

    function it_should_kill_a_lonely_cell_on_the_edge()
    {
        $x = $y = 1;
        $this->setCell($x, $y, true);
        $this->setCell(2, 1, true);
        $this->evaluateCell($x, $y)->shouldReturn(false);
    }

    function it_should_sustain_a_cell_with_enough_neighbors()
    {
        $x = $y = 2;
        $this->setCell($x, $y, true);
        $this->setCell(1, 1, true);
        $this->setCell(2, 1, true);
        $this->evaluateCell($x, $y)->shouldReturn(true);
        $this->setCell(3, 1, true);
        $this->evaluateCell($x, $y)->shouldReturn(true);
    }

    function it_should_kill_an_over_crowded_cell()
    {
        $x = $y = 2;
        $this->setCell($x, $y, true);
        $this->setCell(1, 1, true);
        $this->setCell(2, 1, true);
        $this->setCell(3, 1, true);
        $this->setCell(1, 2, true);
        $this->evaluateCell($x, $y)->shouldReturn(false);
    }

    function it_should_create_a_cell_with_3_neighbors()
    {
        $x = $y = 2;
        $this->setCell($x, $y, false);
        $this->setCell(1, 1, true);
        $this->setCell(2, 1, true);
        $this->setCell(3, 1, true);
        $this->evaluateCell($x, $y)->shouldReturn(true);
    }

    function it_should_still_life_a_block()
    {
        $this->setCell(2, 2, true);
        $this->setCell(2, 3, true);
        $this->setCell(3, 2, true);
        $this->setCell(3, 3, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(3, 3)->shouldEqual(true);
    }

    function it_should_still_life_a_beehive()
    {
        $this->setCell(3, 2, true);
        $this->setCell(4, 2, true);
        $this->setCell(2, 3, true);
        $this->setCell(5, 3, true);
        $this->setCell(3, 4, true);
        $this->setCell(4, 4, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(4, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(5, 3)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(true);
        $this->getCell(4, 4)->shouldEqual(true);
    }

    function it_should_still_life_a_loaf()
    {
        $this->setCell(3, 2, true);
        $this->setCell(4, 2, true);
        $this->setCell(2, 3, true);
        $this->setCell(5, 3, true);
        $this->setCell(3, 4, true);
        $this->setCell(5, 4, true);
        $this->setCell(4, 5, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(4, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(5, 3)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(true);
        $this->getCell(5, 4)->shouldEqual(true);
        $this->getCell(4, 5)->shouldEqual(true);
    }

    function it_should_still_life_a_boat()
    {
        $this->setCell(2, 2, true);
        $this->setCell(3, 2, true);
        $this->setCell(2, 3, true);
        $this->setCell(4, 3, true);
        $this->setCell(3, 4, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 2)->shouldEqual(true);
        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(4, 3)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(true);
    }

    function it_should_oscillate_a_blinker() {
        $this->setCell(2, 3, true);
        $this->setCell(3, 3, true);
        $this->setCell(4, 3, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 3)->shouldEqual(false);
        $this->getCell(3, 3)->shouldEqual(true);
        $this->getCell(4, 3)->shouldEqual(false);
        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(true);

        $this->age(1)->shouldReturn(3);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(3, 3)->shouldEqual(true);
        $this->getCell(4, 3)->shouldEqual(true);
        $this->getCell(3, 2)->shouldEqual(false);
        $this->getCell(3, 4)->shouldEqual(false);
    }

    function it_should_oscillate_a_toad()
    {
        $this->setCell(3, 3, true);
        $this->setCell(4, 3, true);
        $this->setCell(5, 3, true);
        $this->setCell(2, 4, true);
        $this->setCell(3, 4, true);
        $this->setCell(4, 4, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(3, 3)->shouldEqual(false);
        $this->getCell(4, 3)->shouldEqual(false);
        $this->getCell(5, 3)->shouldEqual(true);
        $this->getCell(2, 4)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(false);
        $this->getCell(4, 4)->shouldEqual(false);

        $this->getCell(4, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(5, 4)->shouldEqual(true);
        $this->getCell(3, 5)->shouldEqual(true);

        $this->age(1)->shouldReturn(3);

//        echo $this->render()->getWrappedObject();

        $this->getCell(3, 3)->shouldEqual(true);
        $this->getCell(4, 3)->shouldEqual(true);
        $this->getCell(5, 3)->shouldEqual(true);
        $this->getCell(2, 4)->shouldEqual(true);
        $this->getCell(3, 4)->shouldEqual(true);
        $this->getCell(4, 4)->shouldEqual(true);

        $this->getCell(4, 2)->shouldEqual(false);
        $this->getCell(2, 3)->shouldEqual(false);
        $this->getCell(5, 4)->shouldEqual(false);
        $this->getCell(3, 5)->shouldEqual(false);

    }

    function it_should_oscillate_a_beacon()
    {
        $this->setCell(2, 2, true);
        $this->setCell(3, 2, true);
        $this->setCell(2, 3, true);
        $this->setCell(5, 4, true);
        $this->setCell(4, 5, true);
        $this->setCell(5, 5, true);

//        echo $this->render()->getWrappedObject();

        $this->age(1)->shouldReturn(2);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 2)->shouldEqual(true);
        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(5, 4)->shouldEqual(true);
        $this->getCell(4, 5)->shouldEqual(true);
        $this->getCell(5, 5)->shouldEqual(true);

        $this->getCell(3, 3)->shouldEqual(true);
        $this->getCell(4, 4)->shouldEqual(true);

        $this->age(1)->shouldReturn(3);

//        echo $this->render()->getWrappedObject();

        $this->getCell(2, 2)->shouldEqual(true);
        $this->getCell(3, 2)->shouldEqual(true);
        $this->getCell(2, 3)->shouldEqual(true);
        $this->getCell(5, 4)->shouldEqual(true);
        $this->getCell(4, 5)->shouldEqual(true);
        $this->getCell(5, 5)->shouldEqual(true);

        $this->getCell(3, 3)->shouldEqual(false);
        $this->getCell(4, 4)->shouldEqual(false);
    }
}
