<?php

return [
    // 管理员端 - 首页 - 首页
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/index/index',
        'except'        => ['index', 'view', 'create', 'update', 'delete'],
        'pluralize'     => false,
        'extraPatterns' => [
            'POST login'               => 'login',
            'POST access-token'        => 'access-token',
            'GET person'               => 'person',
            'PUT,PATCH password'       => 'password-change',
            'DELETE cache'             => 'cache-flush',
            'GET table-schema/<table>' => 'table-schema-export',
        ],
    ],

    // 管理员端 - 首页 - 仪表盘
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/index/dashboard',
        'except'        => ['index', 'view', 'create', 'update', 'delete'],
        'pluralize'     => false,
        'extraPatterns' => [
            'GET total' => 'total',
        ],
    ],

    // 管理员端 - 系统管理 - 账户管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/admin/index',
        'extraPatterns' => [
            'PATCH status/<id>'   => 'status',
            'PATCH password/<id>' => 'password-reset',
            'POST role/<id>'      => 'role-assign',
        ],
    ],

    // 管理员端 - 系统管理 - 角色管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/admin/role',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
        ],
    ],

    // 管理员端 - 系统管理 - 权限管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/admin/permission',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
            'GET list' => 'list',
        ],
    ],

    // 管理员端 - 系统管理 - 菜单管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/admin/menu',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
            'GET tree' => 'tree',
        ],
    ],

    // 管理员端 - 系统管理 - 日志管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/admin/log',
        'except'        => ['create', 'update', 'delete'],
    ],

    // 管理员端 - 用户管理 - 用户列表
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/user/index',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
        ],
    ],

    // 管理员端 - 用户管理 - 标签管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/user/tag',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
        ],
    ],

    // 管理员端 - 用户管理 - 日志管理
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/user/log',
        'except'        => ['create', 'update', 'delete'],
    ],

    // 管理员端 - 文件管理 - 文件列表
    [
        'class'         => 'yii\rest\UrlRule',
        'controller'    => 'v1/admin/file/index',
        'extraPatterns' => [
            'PATCH status/<id>' => 'status',
            'POST batch'        => 'batch-create',
        ],
    ],
];
