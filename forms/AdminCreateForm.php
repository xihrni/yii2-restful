<?php

namespace app\forms;

use Yii;
use yii\base\Exception;
use app\modules\v1\admin\resources\AdminResource;
use Throwable;

/**
 * 管理员创建表单
 *
 * Class AdminCreateForm
 * @package app\forms
 */
class AdminCreateForm extends \app\base\BaseModel
{
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
     * @var int [$status = 1] 状态，0=>禁用，1=>正常
     */
    public $status = 1;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $string = ['username', 'password', 'mobile', 'realname'];

        return array_merge(parent::rules(), [
            // 过滤：空字符
            [$string, 'trim'],
            // 过滤：HTML注入
            [$string, 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],

            // 必填
            [['username', 'password', 'mobile'], 'required'],

            // 类型
            [['status'], 'integer'],
            [['username', 'password', 'mobile', 'realname'], 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', '用户名'),
            'password' => Yii::t('app', '密码'),
            'mobile' => Yii::t('app', '手机号码'),
            'realname' => Yii::t('app', '真实姓名'),
            'status' => Yii::t('app', '状态'),
        ];
    }

    /**
     * 提交
     *
     * @return array|mixed 管理员模型对象或模型错误
     * @throws Throwable
     */
    public function submit()
    {
        if (!$this->validate()) {
            return $this->errors;
        }

        return Yii::$app->db->transaction(function ($e) {
            $admin = $this->_createAdmin();
            // TODO: 创建安全日志

            if (!$this->hasErrors()) {
                return $admin;
            } else {
                $e->transaction->rollBack();
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 创建管理员
     *
     * @private
     * @return AdminResource|bool
     * @throws Exception
     */
    private function _createAdmin()
    {
        $model = new AdminResource;
        $model->setPassword($this->password);
        $model->load([
            'username' => $this->username,
            'mobile'   => $this->mobile,
            'realname' => $this->realname,
            'status'   => $this->status,
        ], '');

        if (!$model->save()) {
            $this->addErrors($model->errors);
            return false;
        }

        // 重新查出获取创建时的默认值
        return AdminResource::findOne($model->id);
    }
}
