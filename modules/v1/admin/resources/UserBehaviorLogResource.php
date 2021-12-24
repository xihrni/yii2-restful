<?php

namespace app\modules\v1\admin\resources;

/**
 * 用户行为日志资源
 *
 * Class UserBehaviorLogResource
 * @package app\modules\v1\admin\resources
 */
class UserBehaviorLogResource extends \app\models\UserBehaviorLog
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
            'user' => function ($model) {
                $user = $model->user;

                return $user ? [
                     'id'       => $user->id,
                     'username' => $user->username,
                     'mobile'   => $user->mobile,
                     'realname' => $user->realname,
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
