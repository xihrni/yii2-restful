<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * 管理员权限模型
 *
 * @property int $id
 * @property int $menu_id 菜单ID
 * @property string $title 标题
 * @property string $modules 模块
 * @property string $controller 控制器
 * @property string $action 操作
 * @property string $name 名称（路由）
 * @property string $method 方法
 * @property string $condition 条件（json）
 * @property int $sort 排序
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property AdminAuthMenu $menu
 */
class AdminAuthPermission extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_auth_permission}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['title', 'modules', 'controller', 'action', 'name', 'method', 'condition'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [['id', 'menu_id', 'sort', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['method'], 'string', 'max' => 8],
            [['title'], 'string', 'max' => 32],
            [['modules', 'action'], 'string', 'max' => 64],
            [['name', 'controller'], 'string', 'max' => 128],
            [['condition'], 'string'],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['deleted_at'], 'default', 'value' => null],
            [['title', 'modules', 'controller', 'action', 'name', 'method', 'condition'], 'default', 'value' => ''],
            [['menu_id', 'sort', 'is_trash'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],

            // 唯一索引
            [
                ['title'], 'unique',
                'targetAttribute' => ['menu_id', 'title', 'is_trash', 'deleted_at'],
                'message' => '标题 的值已经被占用了。',
            ],
            [
                ['name'], 'unique',
                'targetAttribute' => ['modules', 'controller', 'action', 'name', 'method', 'is_trash', 'deleted_at'],
                'message' => '名称（路由） 的值已经被占用了。',
            ],

            // 关联
            [
                ['menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdminAuthMenu::className(),
                'targetAttribute' => ['menu_id' => 'id', 'is_trash' => 'is_trash'],
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
            'menu_id' => Yii::t('app', '菜单ID'),
            'title' => Yii::t('app', '标题'),
            'modules' => Yii::t('app', '模块'),
            'controller' => Yii::t('app', '控制器'),
            'action' => Yii::t('app', '操作'),
            'name' => Yii::t('app', '名称（路由）'),
            'method' => Yii::t('app', '方法'),
            'condition' => Yii::t('app', '条件（json）'),
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
    public function getMenu()
    {
        return $this->hasOne(AdminAuthMenu::className(), ['id' => 'menu_id', 'is_trash' => 'is_trash']);
    }
}
