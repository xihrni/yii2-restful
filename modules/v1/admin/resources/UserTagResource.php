<?php

namespace app\modules\v1\admin\resources;

/**
 * 用户标签资源
 *
 * Class UserTagResource
 * @package app\modules\v1\admin\resources
 */
class UserTagResource extends \app\models\UserTag
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
            'name',
            'sort',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
