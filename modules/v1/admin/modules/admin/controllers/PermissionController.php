<?php

namespace app\modules\v1\admin\modules\admin\controllers;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use app\modules\v1\admin\resources\AdminAuthPermissionListResource;

/**
 * 权限管理控制器
 *
 * Class PermissionController
 * @package app\modules\v1\admin\modules\admin\controllers
 */
class PermissionController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\AdminAuthPermissionResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $menuId     = $params['menuId'] ?? null;
            $title      = $params['title'] ?? null;
            $modules    = $params['modules'] ?? null;
            $controller = $params['controller'] ?? null;
            $action     = $params['action'] ?? null;
            $name       = $params['name'] ?? null;
            $method     = $params['method'] ?? null;
            $status     = $params['status'] ?? null;
            /* @var $model ActiveRecord */
            return ($model::find())->where(['is_trash' => 0])
                ->andFilterWhere(['menu_id' => $menuId])
                ->andFilterWhere(['like', 'title', $title])
                ->andFilterWhere(['like', 'modules', $modules])
                ->andFilterWhere(['like', 'controller', $controller])
                ->andFilterWhere(['like', 'action', $action])
                ->andFilterWhere(['like', 'name', $name])
                ->andFilterWhere(['like', 'method', $method])
                ->andFilterWhere(['status' => $status]);
        };
        $actions['index']['sort'] = ['sort' => SORT_DESC, 'id' => SORT_DESC];
        // $actions['index']['pagination'] = ['pageSizeLimit' => [1, 1000]];

        return $actions;
    }

    /**
     * 列表2（用于角色编辑页面选择权限）
     *
     * @return ActiveDataProvider
     */
    public function actionList()
    {
        return new ActiveDataProvider([
            'query' => AdminAuthPermissionListResource::find()->where(['status' => 1, 'is_trash' => 0]),
            'sort' => [
                'defaultOrder' => ['sort' => SORT_DESC, 'id' => SORT_DESC],
            ],
            'pagination' => ['pageSizeLimit' => [1, 10000]],
        ]);
    }
}

