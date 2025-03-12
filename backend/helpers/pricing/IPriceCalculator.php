<?php

namespace app\helpers\pricing;

use DateTime;

interface IPriceCalculator
{
    public function calculate(float $base_price, DateTime $event_date, DateTime $current_date): float;
}
