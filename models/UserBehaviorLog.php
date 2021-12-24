<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * 用户行为日志模型
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $module 模块
 * @property string $controller 控制器
 * @property string $action 操作
 * @property string $route 路由
 * @property string $method 方法
 * @property string $headers 请求头（json）
 * @property string $params 请求参数（json）
 * @property string $body 请求体（json）
 * @property string $authorization 身份认证
 * @property string $request_ip 请求IP
 * @property string $response 响应结果（json）
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property User $user
 */
class UserBehaviorLog extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_behavior_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 类型：整型和数字
            [['id', 'user_id', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['module', 'action'], 'string', 'max' => 64],
            [['controller'], 'string', 'max' => 32],
            [['route', 'authorization'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 8],
            [['request_ip'], 'string', 'max' => 16],
            [['headers', 'params', 'body', 'response'], 'string'],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['user_id', 'deleted_at'], 'default', 'value' => null],
            [[
                'module', 'action', 'route', 'controller', 'method', 'headers', 'params', 'body', 'authorization',
                'request_ip', 'response',
            ], 'default', 'value' => ''],
            [['is_trash'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],

            // 关联
            [
                ['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(),
                'targetAttribute' => ['user_id' => 'id', 'is_trash' => 'is_trash'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'module' => Yii::t('app', '模块'),
            'controller' => Yii::t('app', '控制器'),
            'action' => Yii::t('app', '操作'),
            'route' => Yii::t('app', '路由'),
            'method' => Yii::t('app', '方法'),
            'headers' => Yii::t('app', '请求头（json）'),
            'params' => Yii::t('app', '请求参数（json）'),
            'body' => Yii::t('app', '请求体（json）'),
            'authorization' => Yii::t('app', '身份认证'),
            'request_ip' => Yii::t('app', '请求IP'),
            'response' => Yii::t('app', '响应结果（json）'),
            'is_trash' => Yii::t('app', '是否删除，0=>否，1=>是'),
            'status' => Yii::t('app', '状态，0=>禁用，1=>启用'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'deleted_at' => Yii::t('app', '删除时间'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id', 'is_trash' => 'is_trash']);
    }
}
