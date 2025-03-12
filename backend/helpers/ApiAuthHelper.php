<?php

namespace app\helpers;

use Yii;
use yii\base\ActionFilter;
use yii\web\UnauthorizedHttpException;

class ApiAuthHelper extends ActionFilter
{
    /**
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action): bool
    {
        $apiKey = Yii::$app->request->headers->get('Authorization');
        $method = Yii::$app->request->method;

        if ($method !== 'OPTIONS' && $apiKey !== getenv('API_KEY')) {
            throw new UnauthorizedHttpException('Authorization error - `Authorization` header missing or invalid');
        }

        return parent::beforeAction($action);
    }
}
