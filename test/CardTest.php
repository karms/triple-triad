<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use  \karms\TripleTriad\Card;
use  \karms\TripleTriad\Board;

require __DIR__ . '/../src/autoload.php';

final class CardTest extends TestCase
{
    /**
     * TODO have better rounds internally (Maybe remembering rounds per card?)
     * TODO maybe have coords be a class?
     * TODO more specific exceptions
     * TODO updating color of a card (do we need to remember the original color?)
     * TODO Board::applyRounds is broken (see: have better rounds internally)
     */

    public function testCardCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Card::class,
            new Card('blue', 1, 1, 1, 1)
        );
    }

    public function testBoardCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            Board::class,
            new Board()
        );
    }

    /**
     * @throws Exception
     */
    public function testPlaceAnyCard(): void
    {
        $board = new Board();
        $card = new Card('blue', 1, 1, 1, 1);

        $widths = range(0, $board->getWidth() - 1);
        $heights = range(0, $board->getHeight() - 1);
        foreach ($widths as $x) {
            foreach ($heights as $y) {
                $board->placeCard($card, $x, $y);
                $this->assertInstanceOf(Card::class, $board->getCard($x, $y), "($x:$y)");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function testCannotPlaceCardOutOfBoundsTopLeft(): void
    {
        $board = new Board();
        $card = new Card('blue', 1, 1, 1, 1);

        $this->expectException(\Exception::class);
        $board->placeCard($card, -1, -1);
    }

    /**
     * @throws Exception
     */
    public function testCannotPlaceCardOutOfBoundsBottomRight(): void
    {
        $board = new Board();
        $card = new Card('blue', 1, 1, 1, 1);

        $this->expectException(\Exception::class);
        $board->placeCard($card, $board->getWidth(), $board->getHeight());
    }

    /**
     * @throws Exception
     */
    public function testCannotPlaceCardOnAnotherCard(): void
    {
        $board = new Board();
        $card = new Card('blue', 1, 1, 1, 1);

        $this->expectException(\Exception::class);
        $board->placeCard($card, 0, 0);
        $board->placeCard($card, 0, 0);
    }

    /**
     * @throws Exception
     */
    public function testSameColorWontFlip(): void
    {
        $board = new Board();
        $card = new Card('blue', 1, 1, 1, 5);
        $board->placeCard($card, 0, 0);
        $board->placeCard($card, 1, 0);

        $this->assertEquals('blue', $board->getCard(0, 0)->getColor());
        $this->assertEquals('blue', $board->getCard(1, 0)->getColor());
    }

    /**
     * @throws Exception
     */
    public function testStandardRule(): void
    {
        $board = new Board();
        $board->placeCard(new Card('blue', 1, 1, 1, 1), 0, 0);
        $board->placeCard(new Card('red', 2, 2, 2, 2), 1, 0);

        $this->assertEquals('red', $board->getCard(0, 0)->getColor());
        $this->assertEquals('red', $board->getCard(1, 0)->getColor());
    }

    /**
     * @throws Exception
     */
    public function testSameRuleSameColors(): void
    {
        $board = new Board();
        $board->placeCard(new Card('blue', 1, 1, 1, 1), 0, 0);
        $board->placeCard(new Card('blue', 2, 2, 2, 2), 2, 0);

        $board->placeCard(new Card('red', 1, 1, 2, 1), 1, 0);

        $this->assertEquals('red', $board->getCard(0, 0)->getColor());
        $this->assertEquals('red', $board->getCard(1, 0)->getColor());
        $this->assertEquals('red', $board->getCard(2, 0)->getColor());
    }

    /**
     * @throws Exception
     */
    public function testSameRuleDifferentColors(): void
    {
        $board = new Board();
        $board->placeCard(new Card('red', 1, 1, 1, 1), 0, 0);
        $board->placeCard(new Card('blue', 2, 2, 2, 2), 2, 0);

        $board->placeCard(new Card('red', 1, 1, 2, 1), 1, 0);

        $this->assertEquals('red', $board->getCard(0, 0)->getColor());
        $this->assertEquals('red', $board->getCard(1, 0)->getColor());
        $this->assertEquals('red', $board->getCard(2, 0)->getColor());
    }

    /**
     * @throws Exception
     */
    public function testPlusRuleSameColors(): void
    {
        $board = new Board();
        $board->placeCard(new Card('blue', 1, 1, 5, 1), 0, 0);
        $board->placeCard(new Card('blue', 3, 1, 1, 1), 2, 0);

        $board->placeCard(new Card('red', 4, 1, 4, 1), 1, 0);

        $this->assertEquals('red', $board->getCard(0, 0)->getColor());
        $this->assertEquals('red', $board->getCard(1, 0)->getColor());
        $this->assertEquals('red', $board->getCard(2, 0)->getColor());
    }

    /**
     * @throws Exception
     */
    public function testPlusRuleDifferentColors(): void
    {
        $board = new Board();
        $board->placeCard(new Card('red', 1, 1, 5, 1), 0, 0);
        $board->placeCard(new Card('blue', 3, 1, 1, 1), 2, 0);

        $board->placeCard(new Card('red', 4, 1, 4, 1), 1, 0);

        $this->assertEquals('red', $board->getCard(0, 0)->getColor());
        $this->assertEquals('red', $board->getCard(1, 0)->getColor());
        $this->assertEquals('red', $board->getCard(2, 0)->getColor());
    }


}
