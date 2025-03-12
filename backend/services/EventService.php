<?php

namespace app\services;

use yii\db\ActiveRecord;
use yii\db\Exception;
use app\models\Event;
use app\enums\Event\EventState;
use app\enums\Event\EventStatus;
use app\enums\ModelScenario;
use app\repositories\EventRepository;
use app\exceptions\TicketPulseException;

class EventService
{
    private EventRepository $eventRepository;
    private PricingService $pricingService;

    public function __construct(EventRepository $eventRepository, PricingService $pricingService)
    {
        $this->eventRepository = $eventRepository;
        $this->pricingService = $pricingService;
    }

    /**
     * Fetches upcoming events
     *
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function getUpcomingEvents(int $page = 1, int $page_size = 10): array
    {
        return $this->eventRepository->getUpcomingEvents($page, $page_size);
    }

    /**
     * Fetch a specific event by ID
     *
     * @param int $id
     * @return Event|array|ActiveRecord|null
     */
    public function getEventById(int $id): ?Event
    {
        return $this->eventRepository->getEventById($id);
    }

    /**
     * Save an event
     *
     * @param Event $event
     * @return Event|array|ActiveRecord|null
     * @throws Exception
     */
    public function saveEvent(Event $event): bool
    {
        return $this->eventRepository->save($event);
    }

    /**
     * Create an event
     *
     * @param array $data
     * @return Event
     * @throws TicketPulseException|Exception
     */
    public function createEvent(array $data): Event
    {
        $event = $this->prepareNewEventForSaving($data);
        return $this->savePreparedEvent($event);
    }

    /**
     * Create an event
     *
     * @param int $id
     * @param array $data
     * @return Event
     * @throws TicketPulseException|Exception
     */
    public function updateEvent(int $id, array $data): Event
    {
        $event = $this->prepareExistingEventForSaving($id, $data);
        return $this->savePreparedEvent($event);
    }

    /**
     * @param array $data
     * @return Event
     * @throws TicketPulseException
     */
    public function prepareNewEventForSaving(array $data): Event
    {
        $event = new Event();
        $event->scenario = ModelScenario::CREATE;
        $event->load($data, '');

        $event->is_for_sale = EventState::IS_FOR_SALE;
        $event->status = EventStatus::UPCOMING;
        $event->base_price = Event::assignEventBasePrice($data['event_type']);

        if (!isset($data['current_price'])) {
            $event->current_price = $this->pricingService->calculatePrice($event);
        }

        return $event;
    }

    /**
     * Prepare an existing event for saving (updating)
     *
     * @param int $id
     * @param array $data
     * @return Event
     * @throws TicketPulseException
     */
    public function prepareExistingEventForSaving(int $id, array $data): Event
    {
        $event = $this->getEventById($id);

        if (!$event instanceof Event) {
            throw new TicketPulseException('Event not found', 404);
        }

        $event->scenario = ModelScenario::UPDATE;
        $event->load($data, '');

        if ($data['event_type'] !== $event->event_type) {
            $event->base_price = Event::assignEventBasePrice($data['event_type']);
            $event->current_price = $this->pricingService->calculatePrice($event);
        }

        return $event;
    }

    /**
     * Save an already prepared event (either create or update)
     *
     * @param Event $event
     * @return Event
     * @throws TicketPulseException|Exception
     */
    public function savePreparedEvent(Event $event): Event
    {
        if (!$event->validate()) {
            $error_messages = implode('; ', $event->getFirstErrors());
            throw new TicketPulseException('Validation failed: ' . $error_messages, 422);
        }

        $saved_successfully = $this->saveEvent($event);

        if (!$saved_successfully) {
            throw new TicketPulseException('Error saving event', 400);
        }
        $this->eventRepository->refresh($event);
        return $event;
    }

    /**
     * Update the current price of an event using pricing service
     *
     * @param Event $event
     * @throws Exception|TicketPulseException
     */
    public function updateCurrentPrice(Event $event): void
    {
        if (!$event instanceof Event) {
            throw new TicketPulseException('Event not found', 404);
        }

        $new_price = $this->pricingService->calculatePrice($event);
        if ($event->current_price !== $new_price) {
            $event->current_price = $new_price;
            $this->saveEvent($event);
        }
    }

    /**
     * Batch update Exclusive events to become collectors items
     *
     * @param int $batch_size
     */
    public function batchUpdateExclusivePastEvents(int $batch_size = 50): void
    {
        $this->eventRepository->getExclusivePastEvents()->each($batch_size, function (Event $event) {
            try {
                $this->changeEventToBeCollectorsItem($event);
            } catch (Exception $e) {
                \Yii::error('Error updating exclusive event: ' . $e->getMessage());
            }
        });
    }

    /**
     * Batch update Exclusive events to become collectors items
     *
     * @param int $batch_size
     * @param int $min_days_in_future
     */
    public function batchUpdateNormalEvents(int $batch_size = 50, int $min_days_in_future = 15): void
    {
        $this->eventRepository->getUpcomingBasicAndVipEvents($min_days_in_future)->each($batch_size, function (Event $event) {
            try {
                $this->updateCurrentPrice($event);
            } catch (Exception $e) {
                \Yii::error('Error updating upcoming Basic or VIP event: ' . $e->getMessage());
            }
        });

        $this->eventRepository->getYesterdaysBasicAndVipEvents()->each($batch_size, function (Event $event) {
            try {
                $this->changeEventToBeExpired($event);
            } catch (Exception $e) {
                \Yii::error('Error updating Basic or VIP event: ' . $e->getMessage());
            }
        });
    }

    /**
     * Update the price, status and is_for_sale of an exclusive event
     *
     * @throws Exception|TicketPulseException
     */
    public function changeEventToBeCollectorsItem(Event $event): void
    {
        $event->current_price = $this->pricingService->calculatePrice($event);

        if ($event->status !== EventStatus::COLLECTORS_ITEM) {
            $event->status = EventStatus::COLLECTORS_ITEM;
            $event->is_for_sale = 0;
        }
        $this->saveEvent($event);
    }

    /**
     * Update the price, status and is_for_sale of an expired event
     *
     * @param Event $event
     * @throws Exception
     */
    public function changeEventToBeExpired(Event $event): void
    {
        if ($event->status !== EventStatus::PAST) {
            $event->status = EventStatus::PAST;
            $event->current_price = 0;
            $event->is_for_sale = 0;

            $this->saveEvent($event);
        }
    }
}
