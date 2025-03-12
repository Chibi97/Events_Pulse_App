<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use app\services\EventService;

class UpdateExclusiveEventsJob extends BaseObject implements JobInterface
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
        try {
            $this->eventService->batchUpdateExclusivePastEvents($batch_size);

        } catch (\Exception $e) {
            Yii::error('Error updating exclusive event prices: ' . $e->getMessage());
        }
    }
}
