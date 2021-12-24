<?php

namespace app\components;

use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * HTTP 认证（继承 \yii\filters\auth\HttpBearerAuth 重写错误处理）
 *
 * Class HttpBearerAuth
 * @package app\components
 */
class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException(Yii::t(
            'app/error',
            'Your request was made with invalid credentials.'
        ));
    }
}
