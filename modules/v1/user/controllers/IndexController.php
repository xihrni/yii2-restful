<?php

namespace app\modules\v1\user\controllers;

use app\models\UserSafeLog;
use Yii;
use yii\web\HttpException;
use app\modules\v1\user\resources\UserResource;
use app\forms\LoginForm;
use app\forms\UserCreateForm;
use app\forms\ChangeMobileForm;
use app\forms\ChangePasswordForm;
use xihrni\yii2\behaviors\LogBehavior;
use Throwable;

/**
 * 首页控制器
 *
 * Class IndexController
 * @package app\modules\v1\user\controllers
 */
class IndexController extends \app\base\BaseRestController
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $optional  = ['register', 'login', 'access-token'];

        $behaviors['authenticator']['optional'] = $optional;
        $behaviors['log'] = Yii::$app->params['behaviorLog']['user'];
        $behaviors['log']['class'] = LogBehavior::className();
        $behaviors['log']['optional'] = array_merge($optional, ['password', 'mobile']);

        return $behaviors;
    }

    /**
     * 注册
     */
    public function actionRegister()
    {
        $model = new UserCreateForm(['scenario' => 'user']);
        $model->modelClass = UserResource::className();
        $model->last_login_ip = Yii::$app->request->userIP;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (!is_array($model)) {
            return $model;
        } else {
            throw new HttpException(401, json_encode($model));
        }
    }

    /**
     * 登录
     *
     * @return array|object
     * @throws HttpException
     * @throws Throwable
     */
    public function actionLogin()
    {
        $model = new LoginForm(['scenario' => 'user']);
        $model->modelClass = UserResource::className();
        $model->last_login_ip = Yii::$app->request->userIP;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (!is_array($model)) {
            return $model;
        } else {
            throw new HttpException(401, json_encode($model));
        }
    }

    /**
     * 访问令牌
     *
     * @return array
     * @throws HttpException
     */
    public function actionAccessToken()
    {
        $authorization = Yii::$app->request->post('authorization');

        if (!$authorization) {
            throw new HttpException(400, Yii::t('app/error', 'Parameter error.'));
        }

        $model = UserResource::findIdentityByAuthKey($authorization, Yii::$app->request->userIP);

        if (strtotime($model->last_login_at) < time() - Yii::$app->params['authKeyPeriod']) {
            throw new HttpException(401, Yii::t('app/error', 'Authorization code has expired.'));
        }

        $model->generateAccessToken();
        $model->clearAuthKey();
        if ($model->save(true, ['access_token', 'auth_key'])) {
            return ['access_token' => $model->access_token];
        } else {
            throw new HttpException(500, json_encode($model->errors));
        }
    }

    /**
     * 个人信息
     *
     * @return array
     */
    public function actionPerson()
    {
        $person = Yii::$app->user->identity->toArray();
        unset($person['auth_key']);

        return $person;
    }

    /**
     * 个人信息更新
     *
     * @return UserResource|null
     * @throws HttpException
     */
    public function actionPersonUpdate()
    {
        $model = UserResource::findOne(['id' => Yii::$app->user->id]);
        $model->load(Yii::$app->request->post(), '');

        if ($model->save(true, ['realname', 'nickname', 'avatar', 'sex', 'birthday'])) {
            // 记录日志
            $log = UserSafeLog::create($model->id, $model->id, 7);

            if (is_array($log)) {
                throw new HttpException(500, json_encode($log->errors));
            }

            return $model;
        } else {
            throw new HttpException(500, json_encode($model->errors));
        }
    }

    /**
     * 密码变更
     *
     * @return void
     * @throws HttpException
     * @throws Throwable
     */
    public function actionPasswordChange()
    {
        $model = new ChangePasswordForm(['scenario' => 'user']);
        $model->user = Yii::$app->user->identity;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (is_array($model)) {
            throw new HttpException(422, json_encode($model));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * 手机号码变更
     *
     * @return void
     * @throws HttpException
     * @throws Throwable
     */
    public function actionMobileChange()
    {
        $model = new ChangeMobileForm(['scenario' => 'user']);
        $model->user = Yii::$app->user->identity;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (is_array($model)) {
            throw new HttpException(422, json_encode($model));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
