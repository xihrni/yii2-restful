<?php

namespace app\modules\v1\admin\modules\admin\controllers;

use yii\db\ActiveRecord;

/**
 * 标签管理控制器
 *
 * Class TagController
 * @package app\modules\v1\admin\modules\admin\controllers
 */
class TagController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\UserTagResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $name   = $params['name'] ?? null;
            $status = $params['status'] ?? null;
            /* @var $model ActiveRecord */
            return ($model::find())->where(['is_trash' => 0])
                ->andFilterWhere(['like', 'name', $name])
                ->andFilterWhere(['status' => $status]);
        };
        $actions['index']['sort'] = ['sort' => SORT_DESC, 'id' => SORT_DESC];

        return $actions;
    }
}
