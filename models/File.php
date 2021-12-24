<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * 文件模型
 *
 * @property int $id
 * @property int $admin_id 操作人ID
 * @property int $type 类型，1=>图片，2=>视频，3=>文件
 * @property string $name 名称
 * @property string $path 路径
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property Admin $admin
 */
class File extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['name', 'path'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [['id', 'admin_id', 'type', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['name'], 'string', 'max' => 128],
            [['path'], 'string', 'max' => 255],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['deleted_at'], 'default', 'value' => null],
            [['name', 'path'], 'default', 'value' => ''],
            [['admin_id', 'is_trash'], 'default', 'value' => 0],
            [['type', 'status'], 'default', 'value' => 1],

            // 关联
            [
                ['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(),
                'targetAttribute' => ['admin_id' => 'id', 'is_trash' => 'is_trash'],
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
            'admin_id' => Yii::t('app', 'Admin ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'path' => Yii::t('app', 'Path'),
            'is_trash' => Yii::t('app', 'Is Trash'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id', 'is_trash' => 'is_trash']);
    }
}
