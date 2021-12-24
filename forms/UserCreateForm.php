<?php

namespace app\forms;

use Yii;
use yii\db\ActiveRecord;
use app\models\UserSafeLog;
use Throwable;

/**
 * 用户创建表单
 *
 * Class UserCreateForm
 * @package app\forms
 */
class UserCreateForm extends \app\base\BaseModel
{
    /**
     * @var string $modelClass 模型类
     */
    public $modelClass;

    /**
     * @var array [$tag_ids = []] 标签ID集合（json）
     */
    public $tag_ids = [];

    /**
     * @var string $username 用户名
     */
    public $username;

    /**
     * @var string $password 密码
     */
    public $password;

    /**
     * @var string [$mobile = null] 手机号码
     */
    public $mobile = '';

    /**
     * @var string [$realname = ''] 真实姓名
     */
    public $realname = '';

    /**
     * @var string [$nickname = ''] 昵称
     */
    public $nickname = '';

    /**
     * @var string [$avatar = ''] 头像
     */
    public $avatar = '';

    /**
     * @var int [$sex = 0] 性别，0=>未知，1=>男，2=>女
     */
    public $sex = 0;

    /**
     * @var string [$birthday = '1970-01-01'] 生日
     */
    public $birthday = '1970-01-01';

    /**
     * @var int [$status = 1] 状态，0=>禁用，1=>正常
     */
    public $status = 1;

    /**
     * @var string [$last_login_ip = ''] 最后登录IP
     */
    public $last_login_ip = '';

    /**
     * @var int [$last_login_terminal = 0] 最后登录终端
     */
    public $last_login_terminal = 0;

    /**
     * @var string [$last_login_version = ''] 最后登录版本
     */
    public $last_login_version = '';


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = [
            'username', 'password', 'mobile', 'realname', 'nickname', 'avatar', 'birthday', 'last_login_ip',
            'last_login_version',
        ];

        return array_merge(parent::rules(), [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [['modelClass', 'username', 'password', 'mobile'], 'required'],

            // 类型
            [['sex', 'status', 'last_login_terminal'], 'integer', 'min' => 0],
            [$string, 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'admin' => [
                'modelClass', 'tag_ids', 'username', 'password', 'mobile', 'realname', 'nickname', 'avatar', 'sex',
                'birthday', 'status',
            ],
            'user' => [
                'modelClass', 'username', 'password', 'mobile', 'realname', 'nickname', 'avatar', 'sex', 'birthday',
                'last_login_ip', 'last_login_terminal', 'last_login_version',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'modelClass' => Yii::t('app', '模型类'),
            'tag_ids' => Yii::t('app', '标签ID集合（json）'),
            'username' => Yii::t('app', '用户名'),
            'password' => Yii::t('app', '密码'),
            'mobile' => Yii::t('app', '手机号码'),
            'realname' => Yii::t('app', '真实姓名'),
            'nickname' => Yii::t('app', '昵称'),
            'avatar' => Yii::t('app', '头像'),
            'sex' => Yii::t('app', '性别'),
            'birthday' => Yii::t('app', '生日'),
            'status' => Yii::t('app', '状态'),
            'last_login_ip' => Yii::t('app', '最后登录IP'),
            'last_login_terminal' => Yii::t('app', '最后登录终端'),
            'last_login_version' => Yii::t('app', '最后登录版本'),
        ];
    }

    /**
     * 提交
     *
     * @return array|mixed 用户模型对象或模型错误
     * @throws Throwable
     */
    public function submit()
    {
        if (!$this->validate()) {
            return $this->errors;
        }

        return Yii::$app->db->transaction(function ($e) {
            $user = $this->_createUser();
            // 创建安全日志
            $safeLog = $this->_createUserSafeLog($user);

            if (!$this->hasErrors()) {
                return $user;
            } else {
                $e->transaction->rollBack();
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 创建用户
     *
     * @private
     * @return object|bool
     */
    private function _createUser()
    {
        $modelClass = $this->modelClass;

        $model = new $modelClass;
        $data  = [
            'tag_ids'  => $this->tag_ids,
            'username' => $this->username ? $this->username : 'u' . $this->mobile, // ?? 会判断空字符串为真 ''
            'mobile'   => $this->mobile,
            'realname' => $this->realname,
            'nickname' => $this->nickname ? $this->nickname :
                '用户' . ((int) substr($this->mobile, -6, 6) + (int) rand(12345, 54321)),
            'avatar'   => $this->avatar,
            'sex'      => $this->sex,
            'birthday' => $this->birthday,
            'status'   => $this->status,
        ];

        if ($this->scenario == 'admin') {
            $model->setPassword($this->password);
        } else if ($this->scenario == 'user') {
            $model->generateAuthKey();
            $data['last_login_at']       = date('Y-m-d H:i:s');
            $data['last_login_ip']       = $this->last_login_ip;
            $data['last_login_terminal'] = $this->last_login_terminal;
            $data['last_login_version']  = $this->last_login_version;
        }

        $model->load($data, '');
        if (!$model->save()) {
            $this->addErrors($model->errors);
            return false;
        }

        // 重新查出获取创建时的默认值
        /* @var $modelClass ActiveRecord */
        return $modelClass::findOne($model->id);
    }

    /**
     * 创建用户安全日志
     *
     * @private
     * @param  object $user 用户模型对象
     * @return UserSafeLog|bool|void
     */
    private function _createUserSafeLog($user)
    {
        if (!$this->hasErrors()) {
            if ($this->scenario == 'admin') {
                $operator = Yii::$app->admin->id;
                $operate  = 1;
            } else {
                $operator = Yii::$app->user->id;
                $operate  = 5;
            }

            $model = UserSafeLog::create($user->id, $operator, $operate);

            if (is_array($model)) {
                $this->addErrors($model->errors);
                return false;
            }

            return $model;
        }
    }
}
