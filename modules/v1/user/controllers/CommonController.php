<?php

namespace app\modules\v1\user\controllers;

use Yii;
use yii\base\InvalidConfigException;
use xihrni\yii2\behaviors\LogBehavior;

/**
 * 用户端公共控制器
 *
 * Class CommonController
 * @package app\modules\v1\user\controllers
 */
class CommonController extends \app\base\BaseRestActiveController
{
    /**
     * 行为
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['log'] = Yii::$app->params['behaviorLog']['user'];
        $behaviors['log']['class'] = LogBehavior::className();

        return $behaviors;
    }

    /**
     * 操作
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete'], $actions['status']);

        return $actions;
    }
}
