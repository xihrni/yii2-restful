<?php

namespace app\modules\v1\admin\resources;

/**
 * 管理员资源
 *
 * Class AdminResource
 * @package app\modules\v1\admin\resources
 */
class AdminResource extends \app\models\Admin
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
            'roles' => function ($model) {
                $roles = $this->getRoles()->select(['id', 'name'])->all();

                return $roles ? [
                    'id'   => array_column($roles, 'id'),
                    'name' => array_column($roles, 'name'),
                ] : null;
            },
            'username',
            'mobile',
            'realname',
            'status',
            'created_at',
            'updated_at',
            'last_login_at',
            'last_login_ip',
            'last_login_terminal',
            'last_login_version',
        ];
    }
}
