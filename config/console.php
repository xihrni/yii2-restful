<?php

$config = [
    'id'                  => 'base-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components'          => [
        'cache' => [
            // 'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port'     => 6379,
                'database' => 1,
            ],
            'keyPrefix' => 'restful-api:',
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class'       => 'yii\log\FileTarget',
                    'levels'      => ['info', 'error'],
                    'categories'  => ['sync-delivery-order', 'sync-delivery-order-curl', 'sync-delivery-order-error'], // 需要记录的消息类别列表
                    'logVars'     => ['_SERVER'], // 需要记录在消息中的PHP预定义变量的列表
                    'logFile'     => '@runtime/logs/sync-delivery-order/' . date('Ymd') . '.log', // 日志文件路径
                    'maxFileSize' => 1024, // 最大日志文件大小，以千字节为单位
                    'maxLogFiles' => 50, // 用于轮换的日志文件数
                ]
            ],
        ],
        'formatter'    => [
            'defaultTimeZone' => 'Asia/Shanghai',
            'dateFormat'      => 'yyyy-MM-dd',
            'timeFormat'      => 'HH:mm:ss',
            'datetimeFormat'  => 'yyyy-MM-dd HH:mm:ss',
        ],
        'i18n'  => [
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
        'db'    => require __DIR__ . '/db.php',
        'queue' => [
            'class' => 'app\components\RocketMQ',
        ],
        'push'  => [
            'class' => 'app\components\UniPush',
        ],
    ],
    'params'              => require __DIR__ . '/params.php',
    /*
    'controllerMap'       => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
