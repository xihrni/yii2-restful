<?php

namespace app\forms;

use Yii;
use app\models\UserSafeLog;
use Throwable;

/**
 * 更改密码表单
 *
 * Class ChangePasswordForm
 * @package app\forms
 */
class ChangePasswordForm extends \app\base\BaseModel
{
    /**
     * @var object $user 用户模型对象
     */
    public $user;

    /**
     * @var string $password 密码
     */
    public $password;

    /**
     * @var string $new_password 新密码
     */
    public $new_password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['password', 'new_password'];

        return array_merge(parent::rules(), [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [['password', 'new_password', 'user'], 'required', 'on' => ['admin', 'user']],

            // 类型
            [['password'], 'string'],
            [
                ['new_password'], 'string', 'min' => 6, 'max' => 32,
                'tooShort' => '新密码长度不能小于6位', 'tooLong' => '新密码长度不能大于32位',
            ],

            // 自定义
            [['password'], 'validatePassword'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'admin' => ['user', 'password', 'new_password'],
            'user'  => ['user', 'password', 'new_password'],
        ]);
    }

    /**
     * 验证原密码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->user || !$this->user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app/error', 'Original password error.'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user' => Yii::t('app', '用户模型对象'),
            'password' => Yii::t('app', '密码'),
            'new_password' => Yii::t('app', '新密码'),
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
            $model = $this->_updateUser();
            // 创建用户安全日志
            $this->_createUserSafeLog($model);

            if (!$this->hasErrors()) {
                return $model;
            } else {
                $e->transaction->rollBack();
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 更新用户
     *
     * @private
     * @return bool|object
     */
    private function _updateUser()
    {
        $model = $this->user;
        $model->setPassword($this->new_password);

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
            $model = UserSafeLog::create($user->id, $user->id, 8);

            if (is_array($model)) {
                $this->addErrors($model->errors);
                return false;
            }

            return $model;
        }
    }
}
