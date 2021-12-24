<?php

namespace app\modules\v1\admin\modules\index\controllers;

/**
 * 仪表盘控制器
 *
 * Class DashboardController
 * @package app\modules\v1\admin\modules\index\controllers
 */
class DashboardController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = '';


    /**
     * 操作
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);

        return $actions;
    }

    /**
     * 统计
     *
     * @return array
     */
    public function actionTotal()
    {
        return [];
    }
}
