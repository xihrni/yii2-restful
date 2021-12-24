<?php

namespace app\forms;

use Yii;
use yii\web\UploadedFile;
use app\modules\v1\admin\resources\FileResource;
use Throwable;

/**
 * 文件创建表单
 *
 * Class FileCreateForm
 * @package app\forms
 */
class FileCreateForm extends \app\base\BaseModel
{
    /**
     * @var int $admin_id 管理员ID
     */
    public $admin_id;

    /**
     * @var int $type 类型，1=>图片，2=>视频，3=>文件
     */
    public $type;

    /**
     * @var UploadedFile $file 上传文件对象
     */
    public $file;

    /**
     * @var int [$status = 1] 状态，0=>禁用，1=>启用
     */
    public $status = 1;

    /**
     * @var string $_path 路径
     */
    private $_path;

    /**
     * @var string $_name 名称
     */
    private $_name;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // 必填
            [['admin_id', 'type', 'file'], 'required'],

            // 类型
            [['admin_id', 'type'], 'integer', 'min' => 0],

            // 范围
            ['type', 'in', 'range' => [1, 2, 3]],

            // 自定义
            [['file'], 'validateFile'],
        ]);
    }

    /**
     * 验证上传文件对象
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateFile($attribute, $params)
    {
        if (!$this->hasErrors() && !($this->$attribute instanceof UploadedFile)) {
            $this->addErrorForInvalid($attribute);
        } else {
            $separator   = DIRECTORY_SEPARATOR; // 系统目录分隔符常量
            $this->_path = $separator . 'uploads' . $separator . date('Ymd') . $separator;

            $millisecond = substr(explode(' ', microtime())[0], 2, 3); // 取毫秒
            $this->_name = date('YmdHis') . $millisecond . mt_rand(1000, 9999) . '.' . $this->$attribute->extension;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => Yii::t('app', '管理员ID'),
            'type' => Yii::t('app', '类型，1=>图片，2=>视频，3=>文件'),
            'file' => Yii::t('app', '上传文件对象'),
        ];
    }

    /**
     * 提交
     *
     * @return array|mixed
     * @throws Throwable
     */
    public function submit()
    {
        if (!$this->validate()) {
            return $this->errors;
        }

        return Yii::$app->db->transaction(function ($e) {
            $model = $this->_create();
            $this->_moveFile();

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
     * 创建
     *
     * @private
     * @return FileResource|bool
     */
    private function _create()
    {
        $model = new FileResource;
        $model->load([
            'admin_id' => $this->admin_id,
            'type'     => $this->type,
            'name'     => $this->file->name,
            'path'     => $this->_path . $this->_name,
            'status'   => $this->status,
        ], '');

        if (!$model->save()) {
            $this->addErrors($model->errors);
            return false;
        }

        return $model;
    }

    /**
     * 移动文件
     *
     * @return void
     */
    private function _moveFile()
    {
        if (!$this->hasErrors()) {
            $separator = DIRECTORY_SEPARATOR; // 系统目录分隔符常量
            $realpath  = Yii::$app->basePath . $separator . 'web' . $this->_path; // 文件磁盘绝对路径

            !is_dir($realpath) && mkdir($realpath, 0777, true);
            $this->file->saveAs($realpath . $this->_name);
        }
    }
}
