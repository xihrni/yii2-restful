<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\base\InvalidConfigException;

/**
 * 管理员模型
 *
 * @property int $id
 * @property int $department_id 部门ID
 * @property int $position_id 职位ID
 * @property string $username 用户名
 * @property string $password_hash 加密密码
 * @property string $password_reset_token 重置密码令牌
 * @property string $auth_key 认证密钥
 * @property string $access_token 访问令牌
 * @property string $mobile 手机号码
 * @property string $realname 真实姓名
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
 * @property string $allowance_updated_at 请求更新时间
 *
 * @property AdminAuthAssign[] $adminAuthAssigns
 * @property AdminAuthRole[] $roles
 * @property AdminBehaviorLog[] $adminBehaviorLogs
 * @property File[] $files
 */
class Admin extends \app\base\BaseRateLimitActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['username', 'mobile', 'realname', 'last_login_version'];

        return [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 类型：整型和数字
            [['id', 'is_trash', 'status'], 'integer', 'min' => 0],

            // 类型：字符串
            [['username', 'mobile', 'realname', 'last_login_ip', 'last_login_version'], 'string', 'max' => 16],
            [['password_hash'], 'string', 'max' => 255],
            [['password_reset_token', 'auth_key', 'access_token'], 'string', 'max' => 64],

            // 格式：日期时间
            [['created_at', 'updated_at', 'deleted_at', 'last_login_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss'],

            // 格式：其他
            [['last_login_ip'], 'ip'],
            [['mobile'], 'match', 'pattern' => Yii::$app->params['match']['mobile']],

            // 默认值
            [[
                'password_reset_token', 'auth_key', 'access_token', 'mobile', 'deleted_at', 'last_login_at',
            ], 'default', 'value' => null],
            [['username', 'password_hash', 'realname', 'last_login_ip', 'last_login_version'], 'default', 'value' => ''],
            [['is_trash', 'last_login_terminal', 'allowance', 'allowance_updated_at'], 'default', 'value' => 0],
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'department_id' => Yii::t('app', '部门ID'),
            'position_id' => Yii::t('app', '职位ID'),
            'username' => Yii::t('app', '用户名'),
            'password_hash' => Yii::t('app', '加密密码'),
            'password_reset_token' => Yii::t('app', '重置密码令牌'),
            'auth_key' => Yii::t('app', '认证密钥'),
            'access_token' => Yii::t('app', '访问令牌'),
            'mobile' => Yii::t('app', '手机号码'),
            'realname' => Yii::t('app', '真实姓名'),
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
    public function getAdminAuthAssigns()
    {
        return $this->hasMany(AdminAuthAssign::className(), ['admin_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getRoles()
    {
        return $this->hasMany(AdminAuthRole::className(), ['id' => 'role_id'])
            ->viaTable('{{%admin_auth_assign}}', ['admin_id' => 'id'])
            ->where(['status' => 1, 'is_trash' => 0]);
    }

    /**
     * @return ActiveQuery
     */
    public function getAdminBehaviorLogs()
    {
        return $this->hasMany(AdminBehaviorLog::className(), ['admin_id' => 'id', 'is_trash' => 'is_trash']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['admin_id' => 'id', 'is_trash' => 'is_trash']);
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

        return true;
    }


    /* ----static---- */

    /**
     * 获取权限
     *
     * @param  int $id 主键ID
     * @return array 权限列表
     */
    public static function getPermissions(int $id)
    {
        return Yii::$app->cache->getOrSet('admin:' . $id . ':permissions', function () use ($id) {
            $permissionIds = AdminAuthAssign::find()
                ->alias('aaa')
                ->select(['aar.permission_ids as permission_ids'])
                ->where(['aaa.admin_id' => $id])
                ->leftJoin(AdminAuthRole::tableName() . 'as aar', 'aaa.role_id = aar.id and aar.is_trash = 0 and aar.status = 1')
                ->asArray()
                ->column();

            $permissionIds = array_filter($permissionIds); // 过滤空数据
            $permissionIds = array_map('json_decode', $permissionIds); // JSON转数组
            $permissionIds = array_reduce($permissionIds, 'array_merge', []); // 二维数组合并成一位数组
            $permissionIds = array_unique($permissionIds); // 数组去重

            return AdminAuthPermission::find()
                ->cache()
                ->select(['modules', 'controller', 'action', 'name', 'method', 'condition'])
                ->where(['id' => $permissionIds])
                ->asArray()
                ->all();
        });
    }
}
