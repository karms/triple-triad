<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 2-12-2018
 * Time: 17:15
 */

namespace karms\TripleTriad;

interface CardInterface {
    /**
     * @return string
     */
    public function getColor();

    /**
     * @return int|null
     */
    public function getNorth();

    /**
     * @return int|null
     */
    public function getEast();

    /**
     * @return int|null
     */
    public function getSouth();

    /**
     * @return int|null
     */
    public function getWest();

    /**
     * @param int $x
     * @param int $y
     * @return null
     */
    public function setCoordinates(int $x, int $y);

    /**
     * @return int[]
     */
    public function getCoordinates();
}