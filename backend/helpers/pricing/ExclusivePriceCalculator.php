<?php

namespace app\helpers\pricing;

use DateTime;

class ExclusivePriceCalculator implements IPriceCalculator
{
    public function calculate(float $base_price, DateTime $event_date, DateTime $current_date): float
    {
        $is_past_event = $current_date > $event_date;

        if ($is_past_event) {
            $days_since_event = $event_date->diff($current_date)->days;
            return $base_price + ($base_price * 0.012 * $days_since_event);
        }

        return $base_price;
    }
}
