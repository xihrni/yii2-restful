<?php

return [
    'class'       => 'yii\db\Connection',
    'dsn'         => 'mysql:host=127.0.0.1;dbname=www_xihrni_com',
    'username'    => 'www_xihrni_com',
    'password'    => '123456',
    'charset'     => 'utf8mb4',
    'tablePrefix' => 'xi_',
    'attributes'  => [
        PDO::ATTR_STRINGIFY_FETCHES => false, // 提取的时候将数值转换为字符串，默认为true
        PDO::ATTR_EMULATE_PREPARES  => false, // 启用或禁用预处理语句的模拟，默认为true
    ],

    // Schema cache options (for production environment)
    'enableSchemaCache'   => true,
    'schemaCacheDuration' => 60,
    'schemaCache'         => 'cache',
];
