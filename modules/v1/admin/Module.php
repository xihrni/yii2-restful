<?php

namespace app\modules\v1\admin;

/**
 * admin module definition class
 */
class Module extends \app\base\BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\v1\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $this->modules = [
            // 首页
            'index' => [
                'class' => 'app\modules\v1\admin\modules\index\Module',
                'defaultRoute' => 'index',
            ],
            // 系统管理
            'admin' => [
                'class' => 'app\modules\v1\admin\modules\admin\Module',
                'defaultRoute' => 'index',
            ],
            // 用户管理
            'user' => [
                'class' => 'app\modules\v1\admin\modules\user\Module',
                'defaultRoute' => 'index',
            ],
            // 文件管理
            'file' => [
                'class' => 'app\modules\v1\admin\modules\file\Module',
                'defaultRoute' => 'index',
            ],
        ];
    }
}
