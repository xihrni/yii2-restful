<?php

$admin = require __DIR__ . '/url_admin.php';
$user  = require __DIR__ . '/url_user.php';
$api   = require __DIR__ . '/url_api.php';

return [
    'enablePrettyUrl'     => true,
    'enableStrictParsing' => true,
    'showScriptName'      => false,
    'rules'               => array_merge([
        'GET /'          => 'index/index',
        'GET /index'     => 'index/index',
        'GET /u_s_e_r'   => 'index/user',
        'GET /a_d_m_i_n' => 'index/admin',
    ], $admin, $user, $api),
];
