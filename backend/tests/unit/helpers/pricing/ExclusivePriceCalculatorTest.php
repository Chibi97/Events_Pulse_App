<?php

namespace app\tests\unit\helpers\pricing;

use app\helpers\pricing\ExclusivePriceCalculator;
use app\helpers\pricing\IPriceCalculator;
use PHPUnit\Framework\TestCase;

class ExclusivePriceCalculatorTest extends TestCase
{
    private IPriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new ExclusivePriceCalculator();
    }

    public function testExclusiveTicketPriceBeforeEvent()
    {
        $base_price = 200.00;
        $event_date = new \DateTime('2025-01-01');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(200.00, $calculated_price);
    }

    public function testExclusiveTicketPriceAfterEvent1Day()
    {
        $base_price = 200.00;
        $event_date = new \DateTime('2024-12-31');
        $current_date = new \DateTime('2025-01-01');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(202.40, $calculated_price);
    }

    public function testExclusiveTicketPriceAfterEvent10Days()
    {
        $base_price = 200.00;
        $event_date = new \DateTime('2024-12-31');
        $current_date = new \DateTime('2025-01-10');

        $calculated_price = $this->calculator->calculate($base_price, $event_date, $current_date);
        $this->assertEquals(224.00, $calculated_price);
    }
}
