<?php

namespace app\base;

use Yii;
use yii\filters\RateLimiter;
use yii\base\InvalidConfigException;
use app\models\Setting;
use app\components\HttpBearerAuth;
use app\components\behaviors\ServiceStateBehavior;
use xihrni\yii2\behaviors\SignatureBehavior;

/**
 * 基础REST控制器类
 *
 * Class BaseRestController
 * @package app\base
 */
class BaseRestController extends \yii\rest\Controller
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
                'class' => ServiceStateBehavior::className(),
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
}
