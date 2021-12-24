<?php

namespace app\components\behaviors;

useYii;
use yii\web\HttpException;
use yii\base\InvalidConfigException;

/**
 * 日志行为
 *
 * Class LogBehavior
 * @package app\components\behaviors
 */
class LogBehavior extends \yii\base\ActionFilter
{
    /**
     * @var bool [$switchOn = true] 开关
     */
    public $switchOn = true;

    /**
     * @var string $role 角色
     */
    public $role;

    /**
     * @var string $userBehaviorModel 用户行为模型
     */
    public $userBehaviorModel;


    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->role === null) {
            throw new InvalidConfigException(Yii::t('app/error', '{param} must be set.', ['param' => 'role']));
        }
        if ($this->userBehaviorModel === null) {
            throw new InvalidConfigException(Yii::t('app/error', '{param} must be set.', ['param' => 'userBehaviorModel']));
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\web\HttpException
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

        $request  = Yii::$app->request;
        $headers  = $request->getHeaders()->toArray();
        // 获取模块ID
        $moduleId = $this->_getFullModuleId($action->controller->module, $ids = []);
        $moduleId = implode('/', array_reverse($moduleId));

        $model    = new $this->userBehaviorModel;
        $data     = [
            'module'        => $moduleId,
            'controller'    => $action->controller->id,
            'action'        => $action->id,
            'route'         => $request->pathInfo,
            'method'        => $request->method,
            'headers'       => json_encode($headers),
            'params'        => json_encode($request->get()),
            'body'          => json_encode($request->post()),
            'authorization' => json_encode($headers['authorization']),
            'request_ip'    => $request->userIP,
            'response'      => '',
        ];
        switch ($this->role) {
            case 'admin': $data['admin_id'] = $this->owner->user->id; break;
            case 'user' : $data['user_id']  = $this->owner->user->id; break;
        }
        $model->load($data, '');

        if ($model->save()) {
            return true;
        } else {
            throw new HttpException(422, json_encode($model->errors));
        }
    }


    /* ----private---- */

    /**
     * 获取完整的模块ID
     *
     * @private
     * @param  object $module 模块对象
     * @param  array  $ids    临时存放当前已获取的模块ID
     * @return array
     */
    private function _getFullModuleId($module, &$ids)
    {
        if (isset($module->id)) {
            $ids[] = $module->id;

            if ($module->module) {
                // 过滤框架ID
                if ($module->module->id != Yii::$app->id) {
                    $this->_getFullModuleId($module->module, $ids);
                }
            }
        }

        return $ids;
    }
}
