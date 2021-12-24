<?php

return [
    // 用户端 - 首页
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/user/index',
        'except'        => ['index', 'view', 'create', 'update', 'delete'],
        'pluralize'     => false,
        'extraPatterns' => [
            'POST register'     => 'register',
            'POST login'        => 'login',
            'POST access-token' => 'access-token',
            'GET person'        => 'person',
            'PUT person'        => 'person-update',
            'PUT password'      => 'password-change',
            'PUT mobile'        => 'mobile-change',
        ],
    ],
];
