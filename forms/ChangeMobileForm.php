<?php

namespace app\forms;

use Yii;
use app\models\UserSafeLog;
use Throwable;

/**
 * 更改手机号码表单
 *
 * Class ChangeMobileForm
 * @package app\forms
 */
class ChangeMobileForm extends \app\base\BaseModel
{
    /**
     * @var object $user 用户模型对象
     */
    public $user;

    /**
     * @var string $mobile 手机号码
     */
    public $mobile;

    /**
     * @var string $smscode 短信验证码
     */
    public $smscode;

    /**
     * @var string $new_mobile 新手机号码
     */
    public $new_mobile;

    /**
     * @var string $new_smscode 新短信验证码
     */
    public $new_smscode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['mobile', 'smscode', 'new_mobile', 'new_smscode'];

        return array_merge(parent::rules(), [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [['mobile', 'smscode', 'new_mobile', 'new_smscode', 'user'], 'required'],

            // 类型
            [['mobile', 'smscode', 'new_mobile', 'new_smscode'], 'string'],
            [['smscode', 'new_smscode'], 'string', 'min' => 4, 'max' => 4],

            // 正则
            [['mobile', 'new_mobile'], 'match', 'pattern' => Yii::$app->params['match']['mobile']],

            // 自定义
            [['mobile'], 'validateMobile'],
            [['new_mobile'], 'validateNewMobile'],
            [['smscode'], 'validateSmscode', 'params' => ['mobile' => 'mobile']],
            [['new_smscode'], 'validateSmscode', 'params' => ['mobile' => 'new_mobile']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            'admin' => ['mobile', 'smscode', 'new_mobile', 'new_smscode'],
            'user'  => ['mobile', 'smscode', 'new_mobile', 'new_smscode'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mobile' => Yii::t('app', '手机号码'),
            'smscode' => Yii::t('app', '短信验证码'),
            'new_mobile' => Yii::t('app', '新手机号码'),
            'new_smscode' => Yii::t('app', '新短信验证码'),
            'user' => Yii::t('app', '用户模型对象'),
        ];
    }

    /**
     * 验证短信验证码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateSmscode($attribute, $params)
    {
        $model = $params['mobile'];

        if (!$this->hasErrors()) {
            $smscode = Yii::$app->cache->get('smscode:' . $this->$model);

            if ($this->$attribute != $smscode) {
                if ($this->$attribute != Yii::$app->params['superAk']) {
                    $this->addError($attribute, Yii::t('app/error', 'SMS verification code error.'));
                }
            } else {
                Yii::$app->cache->delete('smscode:' . $this->$model);
            }
        }
    }

    /**
     * 验证原手机号码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateMobile($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->$attribute != $this->user->mobile) {
                $this->addError($attribute, Yii::t('app/error', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attribute),
                ]));
            }
        }
    }

    /**
     * 验证新手机号码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateNewMobile($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->user->find()->where(['mobile' =>$this->$attribute, 'is_trash' => 0])->exists()) {
                $this->addError($attribute, Yii::t('app/error', 'The mobile has been registered.'));
            }
        }
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
            $model = $this->_update();
            // 创建安全日志
            $this->_createUserSafeLog($model);

            if (!$this->hasErrors()) {
                return $model;
            } else {
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 更新
     *
     * @private
     * @return bool|object
     */
    private function _update()
    {
        $model = $this->user;
        $model->mobile = $this->new_mobile;

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
            $model = UserSafeLog::create($user->id, $user->id, 9);

            if (is_array($model)) {
                $this->addErrors($model->errors);
                return false;
            }

            return $model;
        }
    }
}
