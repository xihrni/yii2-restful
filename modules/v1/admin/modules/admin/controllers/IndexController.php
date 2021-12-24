<?php

namespace app\modules\v1\admin\modules\admin\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use app\forms\AdminCreateForm;
use app\forms\AdminAssignRoleForm;
use app\modules\v1\admin\resources\AdminResource;
use Throwable;

/**
 * 账户管理控制器
 *
 * Class IndexController
 * @package app\modules\v1\admin\modules\admin\controllers
 */
class IndexController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\AdminResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $username = $params['username'] ?? null;
            $mobile   = $params['mobile']   ?? null;
            $realname = $params['realname'] ?? null;
            $status   = $params['status']   ?? null;
            /* @var $model ActiveRecord */
            return ($model::find())->where(['is_trash' => 0])
                ->andFilterWhere(['like', 'username', $username])
                ->andFilterWhere(['like', 'mobile', $mobile])
                ->andFilterWhere(['like', 'realname', $realname])
                ->andFilterWhere(['status' => $status]);
        };
        $actions['update']['fields'] = ['mobile', 'realname', 'status'];

        unset($actions['create']);

        return $actions;
    }

    /**
     * 创建
     *
     * @return AdminCreateForm|array|mixed
     * @throws HttpException
     * @throws Throwable
     */
    public function actionCreate()
    {
        $model = new AdminCreateForm;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (!is_array($model)) {
            return $model;
        } else {
            throw new HttpException(422, json_encode($model));
        }
    }

    /**
     * 密码重置
     *
     * @param  int $id 主键
     * @return void
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionPasswordReset($id)
    {
        $model = $this->findModel($id, 0);
        $model->setPassword(AdminResource::DEFAULT_PASSWORD);

        if (!$model->save(true, ['password_hash'])) {
            throw new HttpException(500, json_encode($model->errors));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * 角色分配
     *
     * @param  int $id 主键
     * @return void
     * @throws HttpException
     * @throws Throwable
     */
    public function actionRoleAssign($id)
    {
        $model = new AdminAssignRoleForm;
        $model->admin_id = $id;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (is_array($model)) {
            throw new HttpException(422, json_encode($model));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
