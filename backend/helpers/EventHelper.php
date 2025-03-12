<?php

namespace app\helpers;

use app\exceptions\TicketPulseException;
use app\models\Event;
use DateTime;
use Exception;

class EventHelper
{
    /**
     * @param string $date Event date
     * @throws TicketPulseException|Exception
     */
    public static function validateEventIsInFuture(string $date): bool
    {
        if (empty($date)) {
            throw new TicketPulseException('The event date is missing', 400);
        }

        $eventDate = new DateTime($date);
        $now = new DateTime('now');

        return $eventDate > $now;
    }

    /**
     * @return array
     */
    public static function getEventsDummyData(): array
    {
        return [
            [
                'name' => 'Country Thunder',
                'date' => (new DateTime('+17 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'Basic',
                'image_url' => '/images/1.jpg',
            ],
            [
                'name' => 'Wacken',
                'date' => (new DateTime('+4 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'Basic',
                'image_url' => '/images/2.jpg',
            ],
            [
                'name' => 'Symphonic Gala',
                'date' => (new DateTime('+9 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'Basic',
                'image_url' => '/images/3.jpg',
            ],
            [
                'name' => 'Jazz Night',
                'date' => (new DateTime('+14 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'VIP',
                'image_url' => '/images/4.jpg',
            ],
            [
                'name' => 'Summerbeats',
                'date' => (new DateTime('+1 day'))->format('Y-m-d H:i:s'),
                'event_type' => 'VIP',
                'image_url' => '/images/5.jpg',
            ],
            [
                'name' => 'Tomorrowland',
                'date' => (new DateTime('+5 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'Exclusive',
                'image_url' => '/images/6.jpg',
            ],
            [
                'name' => 'Lollapalooza',
                'date' => (new DateTime('-10 days'))->format('Y-m-d H:i:s'),
                'event_type' => 'Exclusive',
                'image_url' => '/images/7.jpg',
            ],
        ];
    }
}
