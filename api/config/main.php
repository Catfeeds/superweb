<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'defaultRoute' => 'index/index',
    'bootstrap' => ['log'],
    'layout' => false,
    'modules' => [],
    'components' => [
        'request' => [
            'enableCsrfValidation' => false,
            'cookieValidationKey' => '*&#$^$%&^#$%%^&',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    if ($response->isSuccessful == false) {

                        $response->statusCode = isset($response->data->statusCode)? $response->data->statusCode : '500';
                        $response->data = [
                            'error' => $response->statusCode,
                        ];

                    } else {
                        $response->statusCode = 200;
                    }
                }
            },
        ],


        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
            'enableSession' => false,
            'loginUrl' => null,

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
        'errorHandler' => [
            'errorAction' => 'base/error',
        ],

        //配置数据库
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=superweb',
            'username' => 'root',
            'password' => '12345678',
            'charset' => 'utf8',
            'tablePrefix' => 'yii2_',
        ],

       'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            /*'rules' => [
               ['class' => 'yii\rest\UrlRule', 'controller' => 'api'],
           ],*/
        ],

    ],
    'params' => $params,
];
