<?php

namespace app\tests\unit\helpers\pricing;

use app\helpers\pricing\IPriceCalculator;
use app\helpers\pricing\VipPriceCalculator;
use PHPUnit\Framework\TestCase;

class VipPriceCalculatorTest extends TestCase
{
    private IPriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new VipPriceCalculator();
    }

    public function testVipTicketPriceIncrease15to10Days()
    {
        $base_price = 79.99;
        $event_date = new \DateTime('2025-01-16');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEqualsWithDelta(87.99, $calculated_price, 0.01);
    }

    public function testVipTicketPriceIncrease10to5Days()
    {
        $base_price = 79.99;
        $event_date = new \DateTime('2025-01-11');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEqualsWithDelta(95.99, $calculated_price, 0.01);
    }

    public function testVipTicketPriceIncrease5DaysOrLess()
    {
        $base_price = 79.99;
        $event_date = new \DateTime('2025-01-06');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEqualsWithDelta(103.99, $calculated_price, 0.01);
    }

    public function testVipTicketPriceAfterEvent()
    {
        $base_price = 79.99;
        $event_date = new \DateTime('2024-12-31');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(0.0, $calculated_price);
    }
}
