<?php

namespace app\helpers\pricing;

use app\exceptions\TicketPulseException;

class PriceCalculatorFactory
{
    /**
     * @throws TicketPulseException
     */
    public static function create(string $event_type): IPriceCalculator
    {
        switch ($event_type) {
            case 'Basic':
                return new BasicPriceCalculator();
            case 'VIP':
                return new VipPriceCalculator();
            case 'Exclusive':
                return new ExclusivePriceCalculator();
            default:
                throw new TicketPulseException("Unknown event type: $event_type");
        }
    }
}
