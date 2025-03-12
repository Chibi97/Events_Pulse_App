<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use app\services\EventService;

class UpdateNormalEventsJob extends BaseObject implements JobInterface
{
    public EventService $eventService;

    /**
     * @throws NotInstantiableException|InvalidConfigException
     */
    public function __construct($config = [])
    {
        $this->eventService = $config['eventService'] ?? Yii::$container->get(EventService::class);
        parent::__construct($config);
    }

    public function execute($queue): void
    {
        $batch_size = 50;
        $min_days_in_future = 15;
        try {
            $this->eventService->batchUpdateNormalEvents($batch_size, $min_days_in_future);

        } catch (\Exception $e) {
            Yii::error('Error updating Basic and VIP events: ' . $e->getMessage());
        }
    }
}
