<?php

namespace app\base;

use Yii;
use yii\db\ActiveRecord;
use yii\filters\RateLimiter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use app\models\Setting;
use app\components\HttpBearerAuth;
use app\components\behaviors\ServiceStateBehavior;
use xihrni\yii2\behaviors\SignatureBehavior;

/**
 * 基础REST活跃控制器类
 *
 * Class BaseRestActiveController
 * @package app\base
 */
class BaseRestActiveController extends \yii\rest\ActiveController
{
    /**
     * 行为
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'serviceState' => [
                'class'  => ServiceStateBehavior::className(),
                'config' => Setting::findServiceState()['user'],
                'switchOn' => Yii::$app->params['serviceState']['switchOn'],
            ],
            'signature' => [
                'class' => SignatureBehavior::className(),
                'clientSecrets' => Setting::findClientSignatureSecret(),
                'switchOn' => Yii::$app->params['signature']['switchOn'],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'user'  => $this->module->get('user'),
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
                'enableRateLimitHeaders' => true,
            ],
        ]);
    }

    /**
     * 操作
     *
     * @return array
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'xihrni\yii2\restful\actions\IndexAction',
                'modelClass' => $this->modelClass,
            ],
            'view' => [
                'class' => 'xihrni\yii2\restful\actions\ViewAction',
                'modelClass' => $this->modelClass,
            ],
            'create' => [
                'class' => 'xihrni\yii2\restful\actions\CreateAction',
                'modelClass' => $this->modelClass,
            ],
            'update' => [
                'class' => 'xihrni\yii2\restful\actions\UpdateAction',
                'modelClass' => $this->modelClass,
            ],
            'delete' => [
                'class' => 'xihrni\yii2\restful\actions\DeleteAction',
                'modelClass' => $this->modelClass,
            ],
            'status' => [
                'class' => 'xihrni\yii2\restful\actions\StatusAction',
                'modelClass' => $this->modelClass,
            ],
        ]);
    }

    /**
     * 查询模型
     *
     * @param  int|string $id           主键，例：1 或 1,2
     * @param  int        [$status = 1] 状态，0=>不查询状态，1=>查询状态为1的数据
     * @return object
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function findModel($id, $status = 1)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $keys = $modelClass::primaryKey(); // 主键

        if (count($keys) > 1) { // 多主键
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $where = array_combine($keys, $values);
                $where['is_trash'] = 0;
                $where['status'] = $status;
                $model = $modelClass::findOne($where);
            } else {
                throw new HttpException(500, Yii::t('app/error', 'The number of keys and values is inconsistent.'));
            }
        } else if ($id !== null) {
            $where = ['id' => $id, 'is_trash' => 0];
            $status && $where['status'] = $status;
            $model = $modelClass::findOne($where);
        }

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/error', 'Object not found: {id}.', ['id' => $id]));
    }
}
