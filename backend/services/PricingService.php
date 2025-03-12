<?php

namespace app\services;

use DateTime;
use app\exceptions\TicketPulseException;
use yii\base\BaseObject;
use app\helpers\pricing\PriceCalculatorFactory;
use app\models\Event;
use Exception;

class PricingService extends BaseObject
{
    /**
     * Calculate the price of an event ticket based on the rules provided
     *
     * @param Event $event
     * @return float
     * @throws TicketPulseException
     */
    public function calculatePrice(Event $event): float
    {
        try {
            $basePrice = $event->base_price;
            $eventDate = new DateTime($event->date);
            $currentDate = new DateTime();

            $calculator = PriceCalculatorFactory::create($event->event_type);

            return $calculator->calculate($basePrice, $eventDate, $currentDate);
        } catch (Exception $e) {
            throw new TicketPulseException(
                'Failed to calculate price for event: ' . $event->id . ' of type: ' . $event->event_type,
                500
            );
        }
    }
}
