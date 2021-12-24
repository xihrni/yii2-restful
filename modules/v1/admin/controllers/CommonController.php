<?php

namespace app\modules\v1\admin\controllers;

use Yii;
use yii\base\InvalidConfigException;
use app\models\Setting;
use xihrni\yii2\behaviors\LogBehavior;
use xihrni\yii2\behaviors\RbacBehavior;

/**
 * 管理员端公共控制器
 *
 * Class CommonController
 * @package app\modules\v1\admin\controllers
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
        $behaviors['serviceState']['config'] = Setting::findServiceState()['admin'];
        $behaviors['authenticator']['user'] = Yii::$app->admin;
        $behaviors['log'] = Yii::$app->params['behaviorLog']['admin'];
        $behaviors['log']['class'] = LogBehavior::className();
        $behaviors['rbac'] = Yii::$app->params['rbac']['admin'];
        $behaviors['rbac']['class'] = RbacBehavior::className();

        return $behaviors;
    }
}
