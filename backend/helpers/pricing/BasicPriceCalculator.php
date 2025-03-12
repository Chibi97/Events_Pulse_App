<?php

namespace app\helpers\pricing;

use DateTime;

class BasicPriceCalculator implements IPriceCalculator
{
    public function calculate(float $base_price, DateTime $event_date, DateTime $current_date): float
    {
        $days_difference = $current_date->diff($event_date)->days;
        $is_past_event = $current_date > $event_date;

        if ($is_past_event) {
            return 0;
        }

        if ($days_difference <= 5) {
            return $base_price * 1.20;
        } elseif ($days_difference <= 10) {
            return $base_price * 1.10;
        } elseif ($days_difference <= 15) {
            return $base_price * 1.05;
        }

        return $base_price;
    }
}



