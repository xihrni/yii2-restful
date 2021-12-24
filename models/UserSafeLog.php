<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use xihrni\tools\Arrays;

/**
 * 用户安全日志模型
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $operator 操作者
 * @property int $operate 操作，1=>管理员创建，2=>管理员更新，3=>管理员删除，4=>管理员状态变更，5=>用户注册，6=>用户登录，7=>用户更新个人信息，8=>用户密码变更，9=>用户手机号码变更
 * @property string $remark 备注
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property User $user
 */
class UserSafeLog extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_safe_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 类型：整型和数字
            [['id', 'user_id', 'operator', 'operate', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['remark'], 'string', 'max' => 255],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['operator', 'deleted_at'], 'default', 'value' => null],
            [['remark'], 'default', 'value' => ''],
            [['user_id', 'operate', 'is_trash'], 'default', 'value' => 0],
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
            'operator' => Yii::t('app', '操作者'),
            'operate' => Yii::t('app', '操作，1=>管理员创建，2=>管理员更新，3=>管理员删除，4=>管理员状态变更，5=>用户注册，6=>用户登录，7=>用户更新个人信息，8=>用户密码变更，9=>用户手机号码变更'),
            'remark' => Yii::t('app', '备注'),
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


    /* ----custom---- */

    /**
     * 创建
     *
     * @param  int    $userId        用户ID
     * @param  int    $operator      操作者
     * @param  int    $operate       操作
     * @param  string [$remark = ''] 备注
     * @return array|static
     */
    public static function create($userId, $operator, $operate, $remark = '')
    {
        $model  = new static;
        $enum   = Arrays::str2Enum($model->attributeLabels()['operate'])[$operate];
        $remark = $enum . ' ' . ($remark ? ' ' . $remark : '');

        $model->load([
            'user_id'  => $userId,
            'operator' => $operator,
            'operate'  => $operate,
            'remark'   => $remark,
        ], '');

        return !$model->save() ? $model->errors : $model;
    }
}
