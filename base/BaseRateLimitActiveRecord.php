<?php

namespace app\base;

use Yii;
use yii\web\Request;
use yii\base\Action;

/**
 * 基础速率限制活跃记录类
 *
 * Class BaseRateLimitActiveRecord
 * @package app\base
 */
class BaseRateLimitActiveRecord extends BaseIdentityActiveRecord
{
    /**
     * 返回允许的请求的最大数目及时间
     *
     * @param  Request $request
     * @param  Action  $action
     * @return array
     */
    public function getRateLimit($request, $action)
    {
        return Yii::$app->params['rateLimit'];
    }

    /**
     * 返回剩余的允许的请求和最后一次速率限制检查时 相应的 UNIX 时间戳数
     *
     * @param  Request $request
     * @param  Action  $action
     * @return array
     */
    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * 保存剩余的允许请求数和当前的 UNIX 时间戳
     *
     * @param  Request $request
     * @param  Action  $action
     * @param  int     $allowance
     * @param  int     $timestamp
     * @return void
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }
}
