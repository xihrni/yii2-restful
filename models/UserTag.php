<?php

namespace app\models;

use Yii;

/**
 * 用户标签模型
 *
 * @property int $id
 * @property string $name 名称
 * @property int $sort 排序
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 *
 * @property User $user
 */
class UserTag extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['name'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [['id', 'sort', 'is_trash', 'status'], 'integer', 'min' => 0],
            // 类型：字符串
            [['name'], 'string', 'max' => 32],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 默认值
            [['deleted_at'], 'default', 'value' => null],
            [['name'], 'default', 'value' => ''],
            [['sort', 'is_trash'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],

            // 唯一索引
            [
                ['name'], 'unique',
                'targetAttribute' => ['name', 'is_trash', 'deleted_at'],
                'message' => '名称 的值已经被占用了。',
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
            'name' => Yii::t('app', '名称'),
            'sort' => Yii::t('app', '排序'),
            'is_trash' => Yii::t('app', '是否删除，0=>否，1=>是'),
            'status' => Yii::t('app', '状态，0=>禁用，1=>启用'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'deleted_at' => Yii::t('app', '删除时间'),
        ];
    }
}
