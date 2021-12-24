<?php

namespace app\modules\v1\admin\modules\user\controllers;

use yii\db\ActiveRecord;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * 日志管理控制器
 *
 * Class LogController
 * @package app\modules\v1\admin\modules\user\controllers
 */
class LogController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\UserBehaviorLogResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $userId     = $params['userId'] ?? null;
            $module     = $params['module'] ?? null;
            $controller = $params['controller'] ?? null;
            $action     = $params['action'] ?? null;
            $route      = $params['route'] ?? null;
            $method     = $params['method'] ?? null;
            $request_ip = $params['request_ip'] ?? null;
            $status     = $params['status'] ?? null;
            /* @var $model ActiveRecord */
            return ($model::find())->where(['is_trash' => 0])
                ->andFilterWhere(['user_id'    => $userId])
                ->andFilterWhere(['like', 'module', $module])
                ->andFilterWhere(['controller' => $controller])
                ->andFilterWhere(['action'     => $action])
                ->andFilterWhere(['like', 'route', $route])
                ->andFilterWhere(['method'     => $method])
                ->andFilterWhere(['request_ip' => $request_ip])
                ->andFilterWhere(['status'     => $status]);
        };

        unset($actions['view']);

        return $actions;
    }

    /**
     * 详情
     *
     * @param  int $id 主键
     * @return mixed
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model  = $this->findModel($id, 0);
        $result = $model->toArray();
        $result['headers']       = $model->headers;
        $result['params']        = $model->params;
        $result['body']          = $model->body;
        $result['authorization'] = $model->authorization;
        $result['response']      = $model->response;

        return $result;
    }
}
