<?php

namespace app\modules\v1\admin\modules\user\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use app\modules\v1\admin\resources\UserResource;
use app\forms\UserCreateForm;
use Throwable;

/**
 * 用户列表控制器
 *
 * Class IndexController
 * @package app\modules\v1\admin\modules\user\controllers
 */
class IndexController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\UserResource';


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
        $actions['update']['fields'] = [
            'tag_ids', 'username', 'mobile', 'realname', 'nickname', 'avatar', 'sex', 'birthday', 'status',
        ];

        unset($actions['create']);

        return $actions;
    }

    /**
     * 创建
     *
     * @return UserCreateForm|array|mixed
     * @throws HttpException
     * @throws Throwable
     */
    public function actionCreate()
    {
        $model = new UserCreateForm(['scenario' => 'admin']);
        $model->modelClass = UserResource::className();
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (!is_array($model)) {
            return $model;
        } else {
            throw new HttpException(422, json_encode($model));
        }
    }
}
