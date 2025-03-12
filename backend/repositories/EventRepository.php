<?php

namespace app\repositories;

use app\enums\Event\EventStatus;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Exception;
use yii\data\Pagination;
use app\models\Event;
use app\enums\Event\EventType;

class EventRepository
{
    /**
     * Fetches upcoming events with pagination.
     *
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function getUpcomingEvents(int $page = 1, int $page_size = 10): array
    {
        $query = Event::find()->where([
            '>',
            'date',
            new Expression('NOW()')
        ]);

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'page' => $page - 1,
            'pageSize' => $page_size,
        ]);

        $events = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all() ?? [];

        return [
            'data' => $events,
            'pagination' => [
                'totalCount' => $pagination->totalCount,
                'pageCount' => $pagination->getPageCount(),
                'currentPage' => $pagination->getPage() + 1,
                'pageSize' => $pagination->getPageSize(),
            ],
        ];
    }

    /**
     * Fetch a specific event by ID
     *
     * @param int $id
     * @return Event|array|ActiveRecord|null
     */
    public function getEventById(int $id): ?Event
    {
        return Event::findOne($id);
    }

    /**
     * Save an event to the db
     *
     * @param Event $event
     * @return bool
     * @throws Exception
     */
    public function save(Event $event): bool
    {
        return $event->save();
    }

    /**
     * Refresh an event with the latest data
     *
     * @param Event $event
     * @return bool
     */
    public function refresh(Event $event): bool
    {
        return $event->refresh();
    }

    /**
     * Get exclusive events for processing
     *
     * @return ActiveQuery
     */
    public function getExclusivePastEvents(): ActiveQuery
    {
        return Event::find()
            ->where(['event_type' => EventType::EXCLUSIVE])
            ->andWhere(['<', 'date', new Expression('NOW()')])
            ->andWhere(['or',
                ['status' => EventStatus::UPCOMING],
                ['status' => EventStatus::COLLECTORS_ITEM]
            ]);
    }

    /**
     * Get Upcoming (for given min_days_in_future) Basic and VIP events for processing
     *
     * @param int $min_days_in_future
     * @return ActiveQuery
     */
    public function getUpcomingBasicAndVipEvents(int $min_days_in_future): ActiveQuery
    {
        return Event::find()
            ->where(['>', 'date', new Expression('NOW() + INTERVAL :days DAY', [':days' => $min_days_in_future])])
            ->andWhere(['or',
                ['event_type' => EventType::BASIC],
                ['event_type' => EventType::VIP]
            ]);
    }

    /**
     * Get yesterday's Basic and VIP events
     *
     * @return ActiveQuery
     */
    public function getYesterdaysBasicAndVipEvents()
    {
        return Event::find()
            ->where([
                'date' => new Expression('DATE_SUB(NOW(), INTERVAL 1 DAY)')
            ])
            ->andWhere(['or',
                ['event_type' => EventType::BASIC],
                ['event_type' => EventType::VIP]
            ]);
    }

}
