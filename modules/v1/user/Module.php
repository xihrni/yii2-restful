<?php

namespace app\modules\v1\user;

/**
 * user module definition class
 */
class Module extends \app\base\BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\v1\user\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
