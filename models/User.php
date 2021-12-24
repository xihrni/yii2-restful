<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * 用户模型
 *
 * @property int $id
 * @property string $tag_ids 标签ID集合（json）
 * @property string $username 用户名
 * @property string $password_hash 加密密码
 * @property string $password_reset_token 重置密码令牌
 * @property string $auth_key 认证密钥
 * @property string $access_token 访问令牌
 * @property string $mobile 手机号码
 * @property string $realname 真实姓名
 * @property string $nickname 昵称
 * @property string $avatar 头像
 * @property int $sex 性别，0=>未知，1=>男，2=>女
 * @property string $birthday 生日
 * @property int $is_trash 是否删除，0=>否，1=>是
 * @property int $status 状态，0=>禁用，1=>启用
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $deleted_at 删除时间
 * @property string $last_login_at 最后登录时间
 * @property string $last_login_ip 最后登录IP
 * @property int $last_login_terminal 最后登录终端
 * @property string $last_login_version 最后登录版本
 * @property int $allowance 请求剩余次数
 * @property int $allowance_updated_at 请求更新时间
 *
 * @property UserBehaviorLog[] $userBehaviorLogs
 * @property UserSafeLog[] $userSafeLogs
 */
class User extends \app\base\BaseRateLimitActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['username', 'mobile', 'realname', 'avatar', 'last_login_version'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [[
                'id', 'sex', 'is_trash', 'status', 'last_login_terminal', 'allowance', 'allowance_updated_at',
            ], 'integer', 'min' => 0],
            [['tag_ids'], 'each', 'rule' => ['integer']], // 循环

            // 类型：字符串
            [[
                'username', 'mobile', 'realname', 'last_login_ip', 'last_login_version',
            ], 'string', 'max' => 16],
            [['nickname'], 'string', 'max' => 32],
            [['password_reset_token', 'auth_key', 'access_token'], 'string', 'max' => 64],
            [['password_hash', 'avatar'], 'string', 'max' => 255],

            // 格式：日期时间
            [['birthday'], 'date', 'format' => 'yyyy-MM-dd'],
            [['created_at', 'updated_at', 'deleted_at', 'last_login_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 格式：其他
            [['last_login_ip'], 'ip'],
            [['mobile'], 'match', 'pattern' => Yii::$app->params['match']['mobile']],

            // 默认值
            [[
                'password_reset_token', 'auth_key', 'access_token', 'mobile', 'deleted_at', 'last_login_at',
            ], 'default', 'value' => null],
            [[
                'tag_ids', 'username', 'password_hash', 'realname', 'nickname', 'avatar', 'last_login_ip',
                'last_login_version',
            ], 'default', 'value' => ''],
            [['birthday'], 'default', 'value' => '1970-01-01'],

            [[
                'sex', 'is_trash', 'last_login_terminal', 'allowance', 'allowance_updated_at',
            ], 'default', 'value' => 0],
            [['status'], 'default', 'value' => 1],

            // 唯一索引
            [
                ['username'], 'unique',
                'targetAttribute' => ['username', 'is_trash', 'deleted_at'],
                'message' => '用户名 的值已经被占用了。',
            ],
            [
                ['password_reset_token'], 'unique',
                'targetAttribute' => ['password_reset_token', 'is_trash', 'deleted_at'],
                'message' => '重置密码令牌 的值已经被占用了。',
            ],
            [
                ['auth_key'], 'unique',
                'targetAttribute' => ['auth_key', 'is_trash', 'deleted_at'],
                'message' => '认证密钥 的值已经被占用了。',
            ],
            [
                ['access_token'], 'unique',
                'targetAttribute' => ['access_token', 'is_trash', 'deleted_at'],
                'message' => '访问令牌 的值已经被占用了。',
            ],
            [
                ['mobile'], 'unique',
                'targetAttribute' => ['mobile', 'is_trash', 'deleted_at'],
                'message' => '手机号码 的值已经被占用了。',
            ],

            // 自定义
            [['tag_ids'], 'validateRelationIds', 'params' => ['tag_ids' => UserTag::className()]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tag_ids' => Yii::t('app', '标签ID集合（json）'),
            'username' => Yii::t('app', '用户名'),
            'password_hash' => Yii::t('app', '加密密码'),
            'password_reset_token' => Yii::t('app', '重置密码令牌'),
            'auth_key' => Yii::t('app', '认证密钥'),
            'access_token' => Yii::t('app', '访问令牌'),
            'mobile' => Yii::t('app', '手机号码'),
            'realname' => Yii::t('app', '真实姓名'),
            'nickname' => Yii::t('app', '昵称'),
            'avatar' => Yii::t('app', '头像'),
            'sex' => Yii::t('app', '性别，0=>未知，1=>男，2=>女'),
            'birthday' => Yii::t('app', '生日'),
            'is_trash' => Yii::t('app', '是否删除，0=>否，1=>是'),
            'status' => Yii::t('app', '状态，0=>禁用，1=>启用'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'deleted_at' => Yii::t('app', '删除时间'),
            'last_login_at' => Yii::t('app', '最后登录时间'),
            'last_login_ip' => Yii::t('app', '最后登录IP'),
            'last_login_terminal' => Yii::t('app', '最后登录终端'),
            'last_login_version' => Yii::t('app', '最后登录版本'),
            'allowance' => Yii::t('app', '请求剩余次数'),
            'allowance_updated_at' => Yii::t('app', '请求更新时间'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserBehaviorLogs()
    {
        return $this->hasMany(UserBehaviorLog::className(), ['user_id' => 'id', 'is_trash' => 'is_trash']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserSafeLogs()
    {
        return $this->hasMany(UserSafeLog::className(), ['user_id' => 'id', 'is_trash' => 'is_trash']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        // JSON转换数组用于each验证
        if (is_string($this->tag_ids)) {
            $this->tag_ids = json_decode($this->tag_ids, true);
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $isValid = parent::beforeSave($insert);

        if (!$isValid) {
            return $isValid;
        }

        // 更新操作
        if (!$insert) {
            // 不允许更新的字段
            $this->username = $this->oldAttributes['username'];
        }

        // 需要转换为JSON的字段
        $fields = ['tag_ids'];
        foreach ($fields as $field) {
            if (isset($this->$field) && $this->$field !== null && is_array($this->$field)) {
                $this->$field = json_encode($this->$field);
            }
        }

        return true;
    }
}
