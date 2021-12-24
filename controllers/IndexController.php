<?php

namespace app\controllers;

use Yii;
use yii\web\Response;

/**
 * 首页控制器
 *
 * Class IndexController
 * @package app\controllers
 */
class IndexController extends \app\base\BaseController
{
    /**
     * 初始化
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        Yii::$app->response->format = Response::FORMAT_HTML;
    }

    /**
     * 独立操作
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'app\components\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'minLength'       => 4,
                'maxLength'       => 5,
                'foreColor'       => 0x566f7c,
                'backColor'       => 0xf2f2f2,
                'padding'         => 5,
                'offset'          => 4,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->actionUser();
    }

    /**
     * 用户端
     *
     * @return string
     */
    public function actionUser()
    {
        return Yii::$app->cache->getOrSet('index:user', function () {
            return file_get_contents('https://' . Yii::$app->params['domain']['cdn'] . '/html/' . YII_ENV . '/user/index.html?v=' . rand(0, 9));
        }, 10);
    }

    /**
     * 管理员端
     *
     * @return string
     */
    public function actionAdmin()
    {
        return Yii::$app->cache->getOrSet('index:admin', function () {
            return file_get_contents('https://' . Yii::$app->params['domain']['cdn'] . '/html/' . YII_ENV . '/admin/index.html?v=' . rand(0, 9));
        }, 10);
    }
}
