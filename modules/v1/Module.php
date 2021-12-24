<?php

namespace app\modules\v1;

/**
 * v1 module definition class
 */
class Module extends \app\base\BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\v1\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $this->modules = [
            'admin' => [
                'class' => 'app\modules\v1\admin\Module',
                'defaultRoute' => 'index',
            ],
            'user' => [
                'class' => 'app\modules\v1\user\Module',
                'defaultRoute' => 'index',
            ],
            'api' => [
                'class' => 'app\modules\v1\api\Module',
                'defaultRoute' => 'index',
            ],
        ];
    }
}
