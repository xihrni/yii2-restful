<?php

namespace app\modules\v1\user\resources;

use Yii;
use yii\web\Link;
use yii\helpers\Url;
use app\models\UserTag;

/**
 * 用户资源
 *
 * Class UserResource
 * @package app\modules\v1\user\resources
 */
class UserResource extends \app\models\User implements \yii\web\Linkable
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
            'tags' => function ($model) {
                $tag = UserTag::find()->select(['id', 'name'])
                    ->where(['id' => json_decode($model->tag_ids, true)])
                    ->asArray()
                    ->all();

                return $tag ?? [];
            },
            'auth_key',
            'mobile',
            'realname',
            'nickname',
            'avatar',
            'sex',
            'birthday',
        ];
    }

    /**
     * 获取链接
     *
     * @return array
     */
    public function getLinks()
    {
        $version = Yii::$app->params['apiVersion'];

        return [
            Link::REL_SELF    => Url::to(['/' . $version . '/user/index/person'], true),
            'access-token'    => Url::to(['/' . $version . '/user/index/access-token'], true),
            'password-change' => Url::to(['/' . $version . '/user/index/password'], true),
            'mobile-change'   => Url::to(['/' . $version . '/user/index/mobile'], true),
        ];
    }
}
