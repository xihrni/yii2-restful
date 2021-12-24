<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * 管理员角色分配模型
 *
 * @property int $admin_id 管理员ID
 * @property int $role_id 角色ID
 * @property string $created_at 创建时间
 *
 * @property Admin $admin
 * @property AdminAuthRole $role
 */
class AdminAuthAssign extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_auth_assign}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        // 重写父级行为
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 必填
            [['admin_id', 'role_id'], 'required'],

            // 类型：整型和数字
            [['admin_id', 'role_id'], 'integer', 'min' => 0],

            // 格式：日期时间
            [['created_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['admin_id', 'role_id'], 'default', 'value' => 0],

            // 唯一索引
            [['admin_id', 'role_id'], 'unique', 'targetAttribute' => ['admin_id', 'role_id']],

            // 关联
            [
                ['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(),
                'targetAttribute' => ['admin_id' => 'id'],
            ],
            [
                ['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdminAuthRole::className(),
                'targetAttribute' => ['role_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => Yii::t('app', '管理员ID'),
            'role_id' => Yii::t('app', '角色ID'),
            'created_at' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id'])->onCondition(['is_trash' => 1]);
    }

    /**
     * @return ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AdminAuthRole::className(), ['id' => 'role_id'])->onCondition(['is_trash' => 1]);
    }
}
