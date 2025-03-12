<?php

namespace app\helpers;

use app\exceptions\TicketPulseException;
use Yii;
use yii\base\InvalidRouteException;
use yii\web\ErrorHandler;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApiErrorHandler extends ErrorHandler
{
    protected function renderException($exception)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $statusCode = $exception->statusCode ?? 500;
        $message = $exception->getMessage();

        if ($exception instanceof TicketPulseException) {
            $statusCode = $exception->getCode() ?? 400;
        }

        if ($exception instanceof NotFoundHttpException) {
            $previous = $exception->getPrevious();

            if ($previous instanceof InvalidRouteException) {
                $message = 'Route you are trying to query does not exist';
            }
        }

        Yii::$app->response->statusCode = $statusCode;
        Yii::$app->response->data = [
            'code' => $statusCode,
            'message' => $message,
        ];

        if (YII_ENV_DEV) {
            Yii::$app->response->data['type'] = get_class($exception);
        }

        Yii::$app->response->send();
    }
}
