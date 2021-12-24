<?php

namespace app\modules\v1\admin\resources;

/**
 * 管理员角色资源
 *
 * Class AdminAuthRoleResource
 * @package app\modules\v1\admin\resources
 */
class AdminAuthRoleResource extends \app\models\AdminAuthRole
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
            'permission_ids' => function ($model) {
                return $model->permission_ids ? json_decode($model->permission_ids, true) : [];
            },
            'name',
            'description',
            'sort',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
