<?php

namespace app\modules\v1\admin\modules\file\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\web\HttpException;
use app\forms\FileCreateForm;
use app\forms\FileBatchCreateForm;
use Throwable;

/**
 * 文件列表控制器
 *
 * Class IndexController
 * @package app\modules\v1\admin\modules\admin\controllers
 */
class IndexController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\FileResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $name   = $params['name']   ?? null;
            $type   = $params['type']   ?? null;
            $status = $params['status'] ?? null;
            /* @var $model ActiveRecord */
            return ($model::find())->where(['is_trash' => 0])
                ->andFilterWhere(['like', 'name', $name])
                ->andFilterWhere(['type' => $type])
                ->andFilterWhere(['status' => $status]);
        };
        $actions['update']['fields'] = ['name', 'status'];

        unset($actions['create']);

        return $actions;
    }

    /**
     * 创建
     *
     * @return FileCreateForm|array|mixed
     * @throws HttpException
     * @throws Throwable
     */
    public function actionCreate()
    {
        $model = new FileCreateForm;
        $model->admin_id = Yii::$app->admin->id;
        $model->file     = UploadedFile::getInstanceByName('file');
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (!is_array($model)) {
            return $model;
        } else {
            throw new HttpException(422, json_encode($model));
        }
    }

    /**
     * 批量创建
     *
     * @return mixed
     * @throws HttpException
     * @throws Throwable
     */
    public function actionBatchCreate()
    {
        $model = new FileBatchCreateForm;
        $model->admin_id = Yii::$app->admin->id;
        $model->files    = UploadedFile::getInstancesByName('files');
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (array_key_exists('success', $model)) {
            return $model['success'];
        } else {
            throw new HttpException(422, json_encode($model));
        }
    }
}
