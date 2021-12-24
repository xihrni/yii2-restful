<?php

namespace app\modules\v1\admin\resources;

/**
 * 管理员行为日志资源
 *
 * Class AdminBehaviorLogResource
 * @package app\modules\v1\admin\resources
 */
class AdminBehaviorLogResource extends \app\models\AdminBehaviorLog
{
    /**
     * 字段
     *
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'admin' => function ($model) {
                $admin = $model->admin;
                return $admin ? [
                    'id'       => $admin->id,
                     'username' => $admin->username,
                     'mobile'   => $admin->mobile,
                     'realname' => $admin->realname,
                ] : null;
            },
            'module',
            'controller',
            'action',
            'route',
            'method',
            // 'headers', // 详情接口已获取
            // 'params',
            // 'body',
            // 'authorization',
            'request_ip',
            // 'response',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
