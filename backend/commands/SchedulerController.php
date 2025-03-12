<?php

namespace app\commands;

use app\services\EventService;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\di\NotInstantiableException;
use yii\queue\db\Queue;
use app\jobs\UpdateNormalEventsJob;
use app\jobs\UpdateExclusiveEventsJob;

class SchedulerController extends Controller
{
    public Queue $queue;
    public EventService $eventService;

    public function __construct($id, $module, Queue $queue, EventService $eventService, $config = [])
    {
        $this->queue = $queue;
        $this->eventService = $eventService;
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws NotInstantiableException|InvalidConfigException
     */
    public function actionRunDailyJobs()
    {
        $this->queue->push(new UpdateExclusiveEventsJob(['eventService' => $this->eventService]));
        $this->queue->push(new UpdateNormalEventsJob(['eventService' => $this->eventService]));
    }
}
