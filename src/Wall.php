<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 2-12-2018
 * Time: 17:11
 */

namespace karms\TripleTriad;


class Wall implements CardInterface
{
    const POWER = 10;

    public function getColor()
    {
        return null;
    }

    public function getNorth()
    {
        return self::POWER;
    }

    public function getEast()
    {
        return self::POWER;
    }

    public function getSouth()
    {
        return self::POWER;
    }

    public function getWest()
    {
        return self::POWER;
    }


}