<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\helpers\EventHelper;
use app\services\EventService;

class SeedController extends Controller
{

    private EventService $eventService;

    public function __construct($id, $module, EventService $eventService, $config = [])
    {
        $this->eventService = $eventService;
        parent::__construct($id, $module, $config);
    }

    public function actionSeedEvents()
    {
        $events = EventHelper::getEventsDummyData();

        foreach ($events as $data) {
            try {
                $event = $this->eventService->createEvent($data);
                $this->stdout("Successfully inserted the event: {$event->name}\n", Console::FG_GREEN);
            } catch (\Exception $e) {
                $this->stderr("Failed to insert an event.\n", Console::FG_RED);
                $this->stderr("Event Data: " . print_r($data, true));
                $this->stderr("Error: " . $e->getMessage() . "\n");
            }
        }
    }
}
