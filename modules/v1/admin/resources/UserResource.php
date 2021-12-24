<?php

namespace app\modules\v1\admin\resources;

use app\models\UserTag;

/**
 * 用户资源
 *
 * Class UserResource
 * @package app\modules\v1\admin\modules\index\resources
 */
class UserResource extends \app\models\User
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
            'tags'=> function ($model) {
                $tag = UserTag::find()->select(['id', 'name'])
                    ->where(['id' => json_decode($model->tag_ids, true)])
                    ->asArray()
                    ->all();

                return $tag ?? [];
            },
            'username',
            'mobile',
            'realname',
            'nickname',
            'avatar',
            'sex',
            'birthday',
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
