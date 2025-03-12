<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use app\exceptions\TicketPulseException;
use app\services\EventService;
use app\models\Event;

class EventController extends ActiveController
{
    public $modelClass = 'app\models\Event';

    private EventService $eventService;

    public function __construct($id, $module, EventService $eventService, $config = [])
    {
        $this->eventService = $eventService;
        parent::__construct($id, $module, $config);
    }

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * Fetch all upcoming events
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        $page = Yii::$app->request->get('page', 1);
        $page_size = Yii::$app->request->get('pageSize', 10);

        $events = $this->eventService->getUpcomingEvents($page, $page_size);

        return $this->asJson([
            'data' => $events['data'] ?? [],
            'pagination' => $events['pagination']
        ]);
    }

    /**
     * Fetch a specific event by ID
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Response
    {
        $event = $this->eventService->getEventById($id);
        if (!$event instanceof Event) {
            throw new NotFoundHttpException('Event you are trying to query is not found');
        }

        return $this->asJson([
            'data' => $event
        ]);
    }

    /**
     * Create a new event
     *
     * @return Response
     * @throws ServerErrorHttpException|TicketPulseException
     */
    public function actionCreate(): Response
    {
        try {
            $data = Yii::$app->request->bodyParams;
            $event = $this->eventService->createEvent($data);

            return $this->asJson([
                "data" => $event
            ]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
    }

    /**
     * Update an event
     *
     * @param int $id
     * @return Response
     * @throws ServerErrorHttpException|TicketPulseException
     */
    public function actionUpdate(int $id): Response
    {
        try {
            $data = Yii::$app->request->post();
            $event = $this->eventService->updateEvent($id, $data);

            return $this->asJson([
                "data" => $event
            ]);
        } catch (TicketPulseException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
    }
}
