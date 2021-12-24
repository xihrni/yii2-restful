<?php

namespace app\modules\v1\admin\resources;

/**
 * 管理员菜单资源
 *
 * Class AdminAuthMenuResource
 * @package app\modules\v1\admin\resources
 */
class AdminAuthMenuResource extends \app\models\AdminAuthMenu
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
            'parent' => function ($model) {
                $parent = $model->parent;

                return $parent ? [
                    'id'   => $parent->id,
                    'name' => $parent->name,
                ] : null;
            },
            'name',
            'sort',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
