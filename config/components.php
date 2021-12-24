<?php

return [
    'request'      => [
        'cookieValidationKey' => '7izNRLV_CTPD5jyzN4KAkJvKje_rdHPa',
        'parsers' => [
            'application/json' => 'yii\web\JsonParser',
        ],
    ],
    'response'     => [
        'format'  => yii\web\Response::FORMAT_JSON,
        'charset' => 'UTF-8',
        'on beforeSend' => function ($event) {
            $response = $event->sender;
            if (Yii::$app->getRequest()->getMethod() == 'OPTIONS') {
                $response->statusCode = 204;
            } else if ($response->format == yii\web\Response::FORMAT_JSON) {
                $responseData   = $response->data;
                $response->data = [
                    'success' => $response->isSuccessful,
                    'code'    => $response->getStatusCode(),
                ];

                if ($response->isSuccessful) {
                    $response->data['message'] = $response->statusText;
                    $response->data['data']    = $responseData;
                } else {
                    $jsonData = json_decode($responseData['message'], true);

                    $response->data['message'] = $jsonData
                        ? $response->statusText
                        : $response->statusText . ': ' . $responseData['message'];
                    $response->data['data']    = $jsonData ? $jsonData : null;
                }

                $response->statusCode = 200;
            }
        },
    ],
    'cache'        => [
        // 'class' => 'yii\caching\FileCache',
        'class' => 'yii\redis\Cache',
        'redis' => [
            'hostname' => 'localhost',
            'port'     => 6379,
            'database' => 1,
        ],
        'keyPrefix' => 'restful-api:',
    ],
    'errorHandler' => [
        'errorAction' => 'index/error',
    ],
    'mailer'       => [
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => true,
    ],
    'log'          => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'targets'    => [
            [
                'class'  => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ]
        ],
    ],
    'db'           => require __DIR__ . '/db.php',
    'urlManager'   => require __DIR__ . '/url_manager.php',
    'formatter'    => [
        'defaultTimeZone' => 'Asia/Shanghai',
        'dateFormat'      => 'yyyy-MM-dd',
        'timeFormat'      => 'HH:mm:ss',
        'datetimeFormat'  => 'yyyy-MM-dd HH:mm:ss',
    ],
    'i18n'         => [
        'translations' => [
            'app*' => [
                'class'   => 'yii\i18n\PhpMessageSource',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/error' => 'error.php',
                ],
            ],
        ],
    ],
    'user'         => [
        'identityClass'   => 'app\modules\v1\user\resources\UserResource',
        'enableAutoLogin' => true,
        'enableSession'   => false,
        'loginUrl'        => null,
    ],
    'admin'        => [
        'class'           => 'yii\web\User',
        'identityClass'   => 'app\modules\v1\admin\modules\index\resources\AdminResource',
        'enableAutoLogin' => true,
        'enableSession'   => false,
        'loginUrl'        => null,
    ],
];
