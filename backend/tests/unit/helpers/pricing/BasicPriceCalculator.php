<?php

namespace app\tests\unit\helpers\pricing;

use app\helpers\pricing\BasicPriceCalculator;
use app\helpers\pricing\IPriceCalculator;
use PHPUnit\Framework\TestCase;

class BasicPriceCalculatorTest extends TestCase
{
    private IPriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BasicPriceCalculator();
    }

    public function testBasicTicketPriceIncrease15to10Days()
    {
        $base_price = 39.99;
        $event_date = new \DateTime('2025-01-01');
        $current_date = new \DateTime('2025-01-16');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(41.99, $calculated_price);
    }

    public function testBasicTicketPriceIncrease10to5Days()
    {
        $base_price = 39.99;
        $event_date = new \DateTime('2025-01-01');
        $current_date = new \DateTime('2025-01-11');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(43.99, $calculated_price);
    }

    public function testBasicTicketPriceIncrease5DaysOrLess()
    {
        $base_price = 39.99;
        $event_date = new \DateTime('2025-01-01');
        $current_date = new \DateTime('2025-01-06');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(47.99, $calculated_price);
    }

    public function testBasicTicketPriceAfterEvent()
    {
        $base_price = 39.99;
        $event_date = new \DateTime('2024-12-31');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(0.0, $calculated_price);
    }
}
