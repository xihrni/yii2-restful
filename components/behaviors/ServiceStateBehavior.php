<?php

namespace app\components\behaviors;

use Yii;
use yii\web\HttpException;
use yii\base\InvalidConfigException;

/**
 * 服务状态行为
 *
 * Class ServiceStateBehavior
 * @package app\components\behaviors
 */
class ServiceStateBehavior extends \yii\base\ActionFilter
{
    /**
     * @var bool [$switchOn = true] 开关
     */
    public $switchOn = true;

    /**
     * @var array $config 配置数据
     *
     * ```
     * ['status' => 1, 'explain' => 'The service is running.'],
     * ['status' => 0, 'explain' => 'During the system upgrade, please wait for the completion of the upgrade. Sorry for any inconvenience caused to you.'],
     * ```
     */
    public $config;


    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->config === null) {
            throw new InvalidConfigException(Yii::t('app/error', '{param} must be set.', ['param' => 'config']));
        }
    }

    /**
     * @inheritdoc
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        $isPassed = parent::beforeAction($action);
        // 验证父类方法
        if (!$isPassed) {
            return $isPassed;
        }

        // 判断开关
        if (!$this->switchOn) {
            return true;
        }

        if (!$this->config['status']) {
            throw new HttpException(503, $this->config['explain']);
        }

        return true;
    }
}
