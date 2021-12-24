<?php

namespace app\modules\v1\admin\resources;

/**
 * 文件资源
 *
 * Class FileResource
 * @package app\modules\v1\admin\modules\index\resources
 */
class FileResource extends \app\models\File
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
            'admin'=> function ($model) {
                $admin = $model->admin;

                return [
                    'id'       => $admin->id,
                    'realname' => $admin->realname,
                ];
            },
            'type',
            'name',
            'path',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}
