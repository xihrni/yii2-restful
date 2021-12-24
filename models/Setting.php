<?php

namespace app\models;

use Yii;

/**
 * 系统设置模型
 *
 * @property int $id
 * @property string $name 名称
 * @property string $title 标题
 * @property string $value 值（json）
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Setting extends \app\base\BaseActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['value'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [['value'], 'required'],

            // 类型：整型和数字
            [['id', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['value'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 128],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at'], 'datetime', 'format' => 'Y-m-d'],

            // 默认值
            [['deleted_at'], 'default', 'value' => null],
            [['is_trash'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],
            [['value', 'name', 'title'], 'default', 'value' => ''],

            // 唯一索引
            [
                ['name'], 'unique',
                'targetAttribute' => ['username', 'is_trash', 'deleted_at'],
                'message' => '用户名 的值已经被占用了。',
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
            'title' => Yii::t('app', '标题'),
            'value' => Yii::t('app', '值（json）'),
            'is_trash' => Yii::t('app', '是否删除，0=>否，1=>是'),
            'status' => Yii::t('app', '状态，0=>禁用，1=>启用'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'deleted_at' => Yii::t('app', '删除时间'),
        ];
    }


    /* ----static---- */

    /**
     * 查找一条记录根据名称
     *
     * @param  mixed $name 名称
     * @return Setting|null
     */
    public static function findOneByName($name)
    {
        return static::findOne(['name' => $name, 'is_trash' => 0, 'status' => 1]);
    }

    /**
     * 查找服务状态
     *
     * @return mixed
     */
    public static function findServiceState()
    {
        $model = Yii::$app->cache->getOrSet('setting:serviceState', function () {
            return static::findOneByName('serviceState');
        });

        return json_decode($model->value, true);
    }

    /**
     * 查找客户端签名秘钥
     *
     * @return mixed
     */
    public static function findClientSignatureSecret()
    {
        $model = Yii::$app->cache->getOrSet('setting:clientSignatureSecret', function () {
            return static::findOneByName('clientSignatureSecret');
        });

        return json_decode($model->value, true);
    }
}
