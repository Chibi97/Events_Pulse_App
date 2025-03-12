<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'name' => 'Events Pulse',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_SECRET'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'class' => 'app\helpers\ApiErrorHandler',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                // 'OPTIONS api/<controller:\w+>' => '<controller>/options',
                'OPTIONS api/events' => 'event/options',
                'OPTIONS api/carts' => 'cart/options',
                'OPTIONS api/carts/<id:\d+>' => 'cart/options',
                'OPTIONS api/carts/<id:\d+>/items' => 'cart/options',
                'OPTIONS api/carts/<id:\d+>/items/<item_id:\d+>' => 'cart/options',

                'GET api/events' => 'event/index', // Fetch all events
                'GET api/events/<id:\d+>' => 'event/view', // Fetch a specific event
                'PUT api/events/<id:\d+>' => 'event/update', // Update a specific event
                'POST api/events' => 'event/create', // Create a new event
                'GET api/carts/<id:\d+>' => 'cart/view', // Fetch cart with cart_items
                'POST api/carts' => 'cart/create', // Create an empty cart with possibility of creating first cart_item

                'GET api/carts/<id:\+d>/items' => 'cart/view', // Fetch all items in a cart
                'POST api/carts/<id:\d+>/items' => 'cart/add-to-cart', // Add an item to a cart (and create cart if it doesn't exist)
                'PUT api/carts/<id:\d+>/items/<item_id:\d+>' => 'cart/update-cart-item', // Update an item in a cart
                'DELETE api/carts/<id:\d+>/items/<item_id:\d+>' => 'cart/remove-cart-item', // Remove an item from a cart
            ],
        ],
    ],
    'as corsFilter' => [
        'class' => \app\helpers\ApiCorsFilter::class,
    ],
    'as apiAuthentication' => [
        'class' => 'app\helpers\ApiAuthHelper',
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
