<?php

namespace app\helpers;

use Yii;
use yii\base\ActionFilter;
use yii\base\ExitException;

class ApiCorsFilter extends ActionFilter
{
    /**
     * @throws ExitException
     */
    public function beforeAction($action): bool
    {
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', 'http://localhost:8080');
        Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        Yii::$app->response->headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        Yii::$app->response->headers->set('Access-Control-Allow-Credentials', 'true');

        if (Yii::$app->request->getMethod() === 'OPTIONS') {
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->send();
            Yii::$app->end();
        }

        return parent::beforeAction($action);
    }
}
