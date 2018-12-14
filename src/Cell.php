<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 30-11-2018
 * Time: 23:45
 */

namespace karms\TripleTriad;


class Cell
{
    /** @var Card */
    private $card;

    /** @var string */
    private $color;

    /** @var string */
    private $element;

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }


    public function place(Card $card)
    {
        $this->card = $card;
    }

}