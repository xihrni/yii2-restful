<?php

namespace app\modules\v1\admin\modules\index\resources;

use Yii;
use yii\web\Link;
use yii\helpers\Url;

/**
 * 管理员资源
 *
 * Class AdminResource
 * @package app\modules\v1\admin\modules\index\resources
 */
class AdminResource extends \app\models\Admin implements \yii\web\Linkable
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
            'auth_key',
            'mobile' => function ($model) {
                return $model->mobile ? substr_replace($model->mobile, '****', 3, 4) : $model->mobile;
            },
            'realname',
            'roles' => function ($model) {
                return array_column($model->roles, 'name');
            },
            'permissions' => function ($model) {
                $permissions = array_column($model->roles, 'permission_ids'); // 提取权限列
                $permissions = array_filter($permissions); // 过滤空数据
                $permissions = array_map('json_decode', $permissions); // JSON转数组
                $permissions = array_reduce($permissions, 'array_merge', []); // 二维数组合并成一位数组
                $permissions = array_unique($permissions); // 数组去重

                // 重新排序防止索引不连续JSON中返回对象
                sort($permissions);

                return $permissions;
            },
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
            Link::REL_SELF    => Url::to(['/' . $version . '/admin/index/index/person'], true),
            'access-token'    => Url::to(['/' . $version . '/admin/index/index/access-token'], true),
            'password-change' => Url::to(['/' . $version . '/admin/index/index/password'], true),
        ];
    }
}
