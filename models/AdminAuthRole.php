<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\base\InvalidConfigException;

/**
 * 管理员角色模型
 *
 * @property int $id
 * @property string $permission_ids 权限ID集合（json）
 * @property string $name 名称
 * @property string $description 描述
 * @property int $sort 排序
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property AdminAuthAssign[] $adminAuthAssigns
 * @property Admin[] $admins
 */
class AdminAuthRole extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_auth_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['name', 'description'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [['id', 'sort', 'is_trash', 'status'], 'integer', 'min' => 0],
            [['permission_ids'], 'each', 'rule' => ['integer']],

            // 类型：字符串
            [['name'], 'string', 'max' => 32],
            [['description'], 'string', 'max' => 64],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['deleted_at'], 'default', 'value' => null],
            [['name', 'description', 'permission_ids'], 'default', 'value' => ''],
            [['sort', 'is_trash'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],

            // 唯一索引
            [
                ['name'], 'unique',
                'targetAttribute' => ['name', 'is_trash', 'deleted_at'],
                'message' => '名称 的值已经被占用了。',
            ],

            // 关联
            [['permission_ids'], 'each', 'rule' => [
                'exist', 'skipOnError' => true, 'targetClass' => AdminAuthPermission::className(),
                'targetAttribute' => ['permission_ids' => 'id', 'is_trash'],
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'permission_ids' => Yii::t('app', '权限ID集合（json）'),
            'name' => Yii::t('app', '名称'),
            'description' => Yii::t('app', '描述'),
            'sort' => Yii::t('app', '排序'),
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
    public function getAdminAuthAssigns()
    {
        return $this->hasMany(AdminAuthAssign::className(), ['role_id' => 'id', 'is_trash' => 'is_trash']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAdmins()
    {
        return $this->hasMany(Admin::className(), ['id' => 'admin_id'])
            ->viaTable('{{%admin_auth_assign}}', ['role_id' => 'id', 'is_trash' => 'is_trash']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        // JSON转换数组用于each验证
        if (is_string($this->permission_ids)) {
            $this->permission_ids = json_decode($this->permission_ids, true);
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $isValid = parent::beforeSave($insert);

        if (!$isValid) {
            return $isValid;
        }

        // 需要转换为JSON的字段
        $fields = ['permission_ids'];
        foreach ($fields as $field) {
            if (isset($this->$field) && $this->$field !== null && is_array($this->$field)) {
                $this->$field = json_encode($this->$field);
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            // 删除所有用户权限缓存
            $admins = Admin::find()->select(['id'])->asArray()->column();

            foreach ($admins as $v) {
                Yii::$app->cache->delete('admin:' . $v . ':permissions');
            }
        }
    }
}
