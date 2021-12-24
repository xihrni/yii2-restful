<?php

return [
    'adminEmail' => 'admin@xihrni.com',

    'domain' => [
        'dev'  => 'www-dev.xihrni.com',
        'prod' => 'www.xihrni.com',
        'cdn'  => 'cdn.xihrni.com',
        'img'  => 'img.xihrni.com'
    ],

    'authKeyPeriod' => 60 * 5, // 认证密钥周期
    'accessTokenPeriod' => 60 * 60 * 24 * 7, // 访问令牌周期

    'apiVersion' => 'v1', // API版本号
    'rateLimit' => [5, 3], // API访问请求限制 // 5秒内3次

    // 服务状态行为开关
    'serviceState' => [
        'switchOn' => true,
    ],

    // 鉴权行为开关
    'signature' => [
        'switchOn' => false,
    ],

    // RBAC权限认证行为配置
    'rbac' => [
        'admin' => [
            'switchOn' => true,
            'userModel' => 'app\models\Admin',
            'roleModel' => 'app\models\AuthRoleModel',
            'permissionModel' => 'app\models\AuthPermissionModel',
            'assignmentModel' => 'app\models\AuthAssignmentModel',
        ],
    ],

    // 日志行为配置
    'behaviorLog' => [
        'admin' => [
            'switchOn' => true,
            'role' => 'admin',
            'userBehaviorModel' => 'app\models\AdminBehaviorLog',
        ],
        'user' => [
            'switchOn' => true,
            'role' => 'user',
            'userBehaviorModel' => 'app\models\UserBehaviorLog',
        ],
    ],

    'superAk' => '1816', // 超级秘钥

    'match' => [
        'mobile' => '/^1([3456789]{1})\d{9}$/',
    ],
];
