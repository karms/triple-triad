<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 30-11-2018
 * Time: 23:06
 */

namespace karms\TripleTriad;


class Board
{
    const OPEN_RULE = 1 << 0;
    const SAME_RULE = 1 << 1;
    const SAME_WALL_RULE = 1 << 2;
    const PLUS_RULE = 1 << 3;
    const ELEMENTAL_RULE = 1 << 4;
    const SUDDEN_DEATH_RULE = 1 << 5;
    const RANDOM_RULE = 1 << 6;

    const ONE_TRADE_RULE = 'one';
    const DIFFERENCE_TRADE_RULE = 'diff';
    const DIRECT_TRADE_RULE = 'direct';
    const ALL_TRADE_RULE = 'all';


    private $rules = self::OPEN_RULE;

    private $trade_rule = self::ONE_TRADE_RULE;

    /** @var []Card */
    private $board = [];

    /** @var []Card */
    private $next = [];

    /** @var array */
    private $rounds = [];

    /** @var int */
    private $round = 0;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    private $doPropagate = false;

    public function __construct()
    {
        $this->width = 3;
        $this->height = 3;
    }

    /**
     * @param Card $card
     * @param int $x
     * @param int $y
     * @return bool
     * @throws \Exception
     */
    public function placeCard(Card $card, int $x, int $y)
    {
        $this->doPropagate = false;
        $this->rounds = [];
        $this->round = 0;
        if (isset($this->board[$x][$y])) {
            throw new \Exception('cannot place card on another card');
        }

        if ($x >= $this->width
            || $y >= $this->height
            || $x < 0
            || $y < 0
        ) {
            throw new \Exception('Cannot place card out of bounds');
        }

        $card->setCoordinates($x, $y);
        $this->next[$x][$y] = $card;
        $color = $card->getColor();

        $sets = $this->getSets($card, $this->hasRule(self::SAME_WALL_RULE));

        $combos = [
            ['n', 'e'],
            ['n', 's'],
            ['n', 'w'],
            ['e', 's'],
            ['e', 'w'],
            ['s', 'w'],
        ];

        foreach ($combos as $combo) {
            list($a, $b) = $combo;

            if (!isset($sets[$a])) continue;
            if (!isset($sets[$b])) continue;

            $setA = $sets[$a];
            $setB = $sets[$b];

            $cardA = $setA[3];
            $cardB = $setB[3];

            // plus
            if ($setA[0] + $setA[1] == $setB[0] + $setB[1]) {
                if ($cardA && $setA->getColor() != $color) $this->flip($setA[2], $color, true);
                if ($cardB && $setB->getColor() != $color) $this->flip($setB[2], $color, true);
            }

            $this->applyPlusRule($setA, $setB, $color);

            // same
            if ($setA[0] == $setA[1] && $setB[0] == $setB[1]) {
                if ($cardA && $setA->getColor() != $color) $this->flip($setA[2], true);
                if ($cardB && $setB->getColor() != $color) $this->flip($setB[2], true);
            }


            $this->applySameRule($setA, $setB, $color);
        }


        $this->applyHighCardRule($sets, $color);


        if ($this->doPropagate) {
            $this->propagate();
        }

        return !empty($this->rounds);
    }

    /**
     * todo find a way to mark a card to be used for propagation (ie dont propagate cards flipped by high card rule)
     *
     * @param card $card
     * @param string $color
     * @param bool $propagate
     */
    private function flip(card $card, string $color, bool $propagate = false): void
    {
        list($x, $y) = $card->getCoordinates();
        unset($this->board[$x][$y]);
        $card->setColor($color);
        $this->next[$x][$y] = $card;

        $this->rounds[$this->round][] = [$x, $y];
    }


    /**
     * apply high card rule to flipped cards
     *
     */
    public function propagate(): void
    {
        $this->round++;
        foreach ($this->rounds[$this->round - 1] as list($x, $y)) {

            $card = $this->next[$x][$y];
            $color = $card->getColor();
            $sets = $this->getSets($card);

            $this->applyHighCardRule($sets, $color);
        }

        if (!empty($this->rounds[$this->round])) {
            $this->propagate();
        }
    }

    private function hasRule($rule) : bool
    {
        return (bool)$this->rules & $rule;
    }

    private function applyHighCardRule(array $sets, string $color) : void
    {
        foreach ($sets as $set) {
            list($valueA, $valueB, $oppositeCard) = $set;
            if ($oppositeCard && $oppositeCard->getColor() != $color && $valueA > $valueB) {
                $this->flip($oppositeCard, $color);
            }
        }
    }

    private function applySameRule(array $setA, array $setB, string $color) : void
    {
        if( ! $this->hasRule(self::SAME_RULE)) {
            return;
        }

        $cardA = $setA[3]; // might be a wall (aka null)
        $cardB = $setB[3];

        if ($setA[0] == $setA[1] && $setB[0] == $setB[1]) {
            if ($cardA && $setA->getColor() != $color) $this->flip($setA[2], true);
            if ($cardB && $setB->getColor() != $color) $this->flip($setB[2], true);
        }
    }

    private function applyPlusRule(array $setA, array $setB, string $color) : void
    {
        if( ! $this->hasRule(self::PLUS_RULE)) {
            return;
        }

        $cardA = $setA[3]; // might be a wall (aka null)
        $cardB = $setB[3];

        if ($setA[0] + $setA[1] == $setB[0] + $setB[1]) {
            if ($cardA && $setA->getColor() != $color) $this->flip($setA[2], $color, true);
            if ($cardB && $setB->getColor() != $color) $this->flip($setB[2], $color, true);
        }
    }


    /**
     * @param Card $card
     * @param bool $withWall
     * @return array
     */
    public function getSets(Card $card, $withWall = false): array
    {
        list($x, $y) = $card->getCoordinates();

        $north = $this->board[$x - 1][$y] ?? false;
        $east = $this->board[$x][$y + 1] ?? false;
        $south = $this->board[$x + 1][$y] ?? false;
        $west = $this->board[$x][$y - 1] ?? false;

        $sets = [];
        if ($north) {
            $sets['n'] = [$card->getNorth(), $north->getSouth(), $north];
        }
        if ($east) {
            $sets['e'] = [$card->getEast(), $east->getWest(), $east];
        }
        if ($south) {
            $sets['s'] = [$card->getSouth(), $south->getNorth(), $south];
        }
        if ($west) {
            $sets['w'] = [$card->getWest(), $west->getEast(), $west];
        }

        if ($withWall) {
            if (!$north && $y == 0) {
                $sets['n'] = [$card->getNorth(), 10, null];
            }
            if (!$east && $x == $this->width) {
                $sets['e'] = [$card->getEast(), 10, null];
            }
            if (!$south && $y == $this->height) {
                $sets['s'] = [$card->getSouth(), 10, null];
            }
            if (!$west && $x == 0) {
                $sets['w'] = [$west->getNorth(), 10, null];
            }
        }

        return $sets;
    }
}