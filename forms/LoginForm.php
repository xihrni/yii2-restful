<?php

namespace app\forms;

use Yii;
use app\base\BaseIdentityActiveRecord;
use app\models\UserSafeLog;
use Throwable;

/**
 * 登录表单
 *
 * Class LoginForm
 * @package app\forms
 */
class LoginForm extends \app\base\BaseModel
{
    /**
     * @var string $modelClass 模型类
     */
    public $modelClass;

    /**
     * @var string $last_login_ip 最后登录IP
     */
    public $last_login_ip;

    /**
     * @var string $username 用户名
     */
    public $username;

    /**
     * @var string $password 密码
     */
    public $password;

    /**
     * @var int $last_login_terminal 最后登录终端
     */
    public $last_login_terminal;

    /**
     * @var string $last_login_version 最后登录版本
     */
    public $last_login_version;

    /**
     * @var object $_user 用户模型对象
     */
    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['username', 'password', 'last_login_version'];

        return array_merge(parent::rules(), [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [[
                'modelClass', 'last_login_ip', 'username', 'password', 'last_login_terminal', 'last_login_version',
            ], 'required', 'on' => ['admin', 'user']],

            // 类型
            [['last_login_terminal'], 'integer'],
            [['modelClass', 'last_login_ip', 'username', 'password', 'last_login_version'], 'string'],

            // 自定义
            [['modelClass'], 'validateModelClass'],
            [['username'], 'validateUsername'],
            [['password'], 'validatePassword'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'admin' => ['modelClass', 'last_login_ip', 'username', 'password', 'last_login_terminal', 'last_login_version'],
            'user'  => ['modelClass', 'last_login_ip', 'username', 'password', 'last_login_terminal', 'last_login_version'],
        ]);
    }

    /**
     * 验证模型类
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateModelClass($attribute, $params)
    {
        if (!$this->hasErrors() && !class_exists($this->$attribute)) {
            $this->addErrorForInvalid($attribute);
            /*
            if (!($this->user instanceof \yii\web\IdentityInterface)) {
                $this->addError($attribute, Yii::t('app', '用户对象不存在'));
            }
            */
        }
    }

    /**
     * 验证用户名
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors() && !$this->user) {
            $this->addError($attribute, Yii::t('app/error', 'Incorrect username or password.'));
        }
    }

    /**
     * 验证密码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors() && !$this->user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('app/error', 'Incorrect username or password.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'modelClass' => Yii::t('app', '模型类'),
            'last_login_ip' => Yii::t('app', '最后登录IP'),
            'username' => Yii::t('app', '用户名'),
            'password' => Yii::t('app', '密码'),
            'last_login_terminal' => Yii::t('app', '最后登录终端'),
            'last_login_version' => Yii::t('app', '最后登录版本号'),
        ];
    }

    /**
     * 提交
     *
     * @return array|object 用户模型对象或模型错误
     * @throws Throwable
     */
    public function submit()
    {
        if (!$this->validate()) {
            return $this->errors;
        }

        return Yii::$app->db->transaction(function ($e) {
            // 更新用户
            $user = $this->_updateUser();
            // 创建用户安全日志
            $this->_createUserSafeLog($user);

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
     * 获取用户
     *
     * @protected
     * @return object|null 用户模型对象或空
     */
    protected function getUser()
    {
        if (!$this->_user) {
            /* @var $model BaseIdentityActiveRecord */
            $model = $this->modelClass;

            switch ($this->scenario) {
                case 'admin' :
                case 'user'  : $this->_user = $model::findIdentityByUsername($this->username); break;
            }
        }

        return $this->_user;
    }

    /**
     * 更新用户
     *
     * @private
     * @return object|bool
     */
    private function _updateUser()
    {
        $model = $this->user;
        $model->generateAuthKey();
        $model->clearAccessToken();
        $model->last_login_at = date('Y-m-d H:i:s');
        $model->last_login_ip = $this->last_login_ip;
        $model->last_login_terminal = $this->last_login_terminal;
        $model->last_login_version  = $this->last_login_version;

        if (!$model->save()) {
            $this->addErrors($model->errors);
            return false;
        }

        return $model;
    }

    /**
     * 创建用户安全日志
     *
     * @private
     * @param  object $user 用户模型对象
     * @return void|bool|UserSafeLog
     */
    private function _createUserSafeLog($user)
    {
        if (!$this->hasErrors() && $this->scenario == 'user') { // TODO: 管理员
            $model = UserSafeLog::create($user->id, $user->id, 6);

            if (is_array($model)) {
                $this->addErrors($model->errors);
                return false;
            }

            return $model;
        }
    }
}
