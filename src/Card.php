<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 30-11-2018
 * Time: 23:00
 */

namespace karms\TripleTriad;

class Card implements CardInterface
{
    private $color;

    private $north;
    private $east;
    private $south;
    private $west;

    private $element;

    private $x;
    private $y;


    public function __construct($color, $north, $east, $south, $west, $element = 0)
    {
        $this->color = $color;

        $this->north = $north;
        $this->east = $east;
        $this->south = $south;
        $this->west = $west;

        $this->element = $element;
    }


    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }


    public function hasColor(string $color)
    {
        return $this->color == $color;
    }


    /**
     * @return mixed
     */
    public function getNorth()
    {
        return $this->north;
    }

    /**
     * @return mixed
     */
    public function getEast()
    {
        return $this->east;
    }

    /**
     * @return mixed
     */
    public function getSouth()
    {
        return $this->south;
    }

    /**
     * @return mixed
     */
    public function getWest()
    {
        return $this->west;
    }

    /**
     * @return mixed
     */
    public function getElement()
    {
        return $this->element;
    }

    public function setCoordinates(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getCoordinates()
    {
        return [$this->x, $this->y];
    }


}