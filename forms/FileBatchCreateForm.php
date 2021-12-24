<?php

namespace app\forms;

use Yii;
use yii\db\Exception;
use yii\web\UploadedFile;
use app\modules\v1\admin\resources\FileResource;
use Throwable;

/**
 * 文件批量创建表单
 *
 * Class FileBatchCreateForm
 * @package app\forms
 */
class FileBatchCreateForm extends \app\base\BaseModel
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
     * @var UploadedFile $files 上传文件对象
     */
    public $files;

    /**
     * @var int [$status = 1] 状态，0=>禁用，1=>启用
     */
    public $status = 1;

    /**
     * @var string $_path 路径
     */
    private $_path;

    /**
     * @var array $_names 名称
     */
    private $_names;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // 必填
            [['admin_id', 'type', 'files'], 'required'],

            // 类型
            [['admin_id', 'type'], 'integer', 'min' => 0],
            [['files'], 'file', 'maxFiles' => 10],

            // 范围
            ['type', 'in', 'range' => [1, 2, 3]],

            // 自定义
            [['files'], 'validateFiles'],
        ]);
    }

    /**
     * 验证上传文件对象
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return void
     */
    public function validateFiles($attribute, $params)
    {
        if (!$this->hasErrors() && !($this->$attribute[0] instanceof UploadedFile)) {
            $this->addErrorForInvalid($attribute);
        } else {
            $separator   = DIRECTORY_SEPARATOR; // 系统目录分隔符常量
            $this->_path = $separator . 'uploads' . $separator . date('Ymd') . $separator;

            $millisecond = substr(explode(' ', microtime())[0], 2, 3); // 取毫秒

            // 新命名
            foreach ($this->$attribute as $v) {
                $this->_names[] = date('YmdHis') . $millisecond . mt_rand(1000, 9999) . '.' . $v->extension;
            }
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
            'files' => Yii::t('app', '上传文件对象'),
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
            $model = $this->_batchCreate();
            $this->_batchMoveFile();

            if (!$this->hasErrors()) {
                return ['success' => $model];
            } else {
                $e->transaction->rollBack();
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 批量创建
     *
     * @private
     * @return FileResource[]
     * @throws Exception
     */
    private function _batchCreate()
    {
        $fields = ['admin_id', 'type', 'name', 'path', 'status'];
        $rows   = [];
        foreach ($this->files as $k => $v) {
            $rows[] = [
                $this->admin_id,
                $this->type,
                $v->name,
                $this->_path . $this->_names[$k],
                $this->status,
            ];
        }

        $affect = Yii::$app->db->createCommand()
            ->batchInsert(FileResource::tableName(), $fields, $rows)
            ->execute();
        $lastId = Yii::$app->db->lastInsertID;

        return FileResource::find()->where(['between', 'id', $lastId, $lastId + $affect - 1])->all();
    }

    /**
     * 批量移动文件
     *
     * @return void
     */
    private function _batchMoveFile()
    {
        if (!$this->hasErrors()) {
            $separator = DIRECTORY_SEPARATOR; // 系统目录分隔符常量
            $realpath  = Yii::$app->basePath . $separator . 'web' . $this->_path; // 文件磁盘绝对路径

            !is_dir($realpath) && mkdir($realpath, 0777, true);
            foreach ($this->files as $k => $v) {
                $v->saveAs($realpath . $this->_names[$k]);
            }
        }
    }
}
