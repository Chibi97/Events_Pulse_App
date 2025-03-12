<?php

namespace app\tests\unit\helpers\pricing;

use app\exceptions\TicketPulseException;
use app\helpers\pricing\BasicPriceCalculator;
use app\helpers\pricing\ExclusivePriceCalculator;
use app\helpers\pricing\IPriceCalculator;
use app\helpers\pricing\PriceCalculatorFactory;
use app\helpers\pricing\VipPriceCalculator;
use PHPUnit\Framework\TestCase;

class PriceCalculatorFactoryTest extends TestCase
{
    public function testCreateBasicPriceCalculator()
    {
        $calculator = PriceCalculatorFactory::create('Basic');
        $this->assertInstanceOf(IPriceCalculator::class, $calculator);
        $this->assertInstanceOf(BasicPriceCalculator::class, $calculator);
    }

    public function testCreateVipPriceCalculator()
    {
        $calculator = PriceCalculatorFactory::create('VIP');
        $this->assertInstanceOf(IPriceCalculator::class, $calculator);
        $this->assertInstanceOf(VipPriceCalculator::class, $calculator);
    }

    public function testCreateExclusivePriceCalculator()
    {
        $calculator = PriceCalculatorFactory::create('Exclusive');
        $this->assertInstanceOf(IPriceCalculator::class, $calculator);
        $this->assertInstanceOf(ExclusivePriceCalculator::class, $calculator);
    }

    public function testCreateThrowsExceptionForUnknownEventType()
    {
        $this->expectException(TicketPulseException::class);
        PriceCalculatorFactory::create('Unknown');
    }
}
