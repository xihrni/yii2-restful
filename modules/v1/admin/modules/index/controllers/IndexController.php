<?php

namespace app\modules\v1\admin\modules\index\controllers;

use Yii;
use yii\web\HttpException;
use app\models\Setting;
use app\modules\v1\admin\modules\index\resources\AdminResource;
use app\forms\LoginForm;
use app\forms\ChangePasswordForm;
use xihrni\yii2\behaviors\LogBehavior;
use xihrni\yii2\behaviors\RbacBehavior;
use Throwable;

/**
 * 首页控制器
 *
 * Class IndexController
 * @package app\modules\v1\admin\modules\index\controllers
 */
class IndexController extends \app\base\BaseRestController
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $optional  = ['login', 'access-token'];

        $behaviors['serviceState']['config'] = Setting::findServiceState()['admin'];
        $behaviors['authenticator']['user'] = Yii::$app->admin;
        $behaviors['authenticator']['optional'] = $optional;
        $behaviors['log'] = Yii::$app->params['behaviorLog']['admin'];
        $behaviors['log']['class'] = LogBehavior::className();
        $behaviors['log']['optional'] = array_merge($optional, ['password']);
        $behaviors['rbac'] = Yii::$app->params['rbac']['admin'];
        $behaviors['rbac']['class'] = RbacBehavior::className();
        $behaviors['rbac']['optional'] = $optional;

        return $behaviors;
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
        $model = new LoginForm(['scenario' => 'admin']);
        $model->modelClass = AdminResource::className();
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

        $model = AdminResource::findIdentityByAuthKey($authorization, Yii::$app->request->userIP);

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
        $person = Yii::$app->admin->identity->toArray();
        unset($person['auth_key']);

        return $person;
    }

    /**
     * 密码变更
     *
     * @throws HttpException
     * @throws Throwable
     */
    public function actionPasswordChange()
    {
        $model = new ChangePasswordForm;
        $model->user = Yii::$app->admin->identity;
        $model->load(Yii::$app->request->post(), '');
        $model = $model->submit();

        if (is_array($model)) {
            throw new HttpException(422, json_encode($model));
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * 缓存刷新
     *
     * @return void
     */
    public function actionCacheFlush()
    {
        Yii::$app->cache->flush();
    }

    /**
     * 表设计导出（Markdown格式）
     *
     * @param  string $table 表名
     * @return void
     * @throws HttpException
     */
    public function actionTableSchemaExport($table)
    {
        $schema = Yii::$app->db->getTableSchema('{{%' . $table . '}}');
        if (!$schema) {
            throw new HttpException(400, Yii::t('app/error', '{attribute} is invalid.', ['attribute' => $table]));
        }

        foreach ($schema->columns as $v) {
            echo '| ';
            echo $v->name . ' | ';
            echo $v->dbType . ' | ';
            echo ($v->allowNull ? 'true' : 'false') . ' | ';

            if ($v->defaultValue === null) {
                echo 'NULL';
            } else if ($v->defaultValue === '') {
                echo '""';
            } else if ($v->defaultValue === 0 || $v->defaultValue === '0') {
                echo 0;
            } else if (is_object($v->defaultValue)) {
                echo $v->defaultValue->expression;
            } else {
                echo $v->defaultValue;
            }
            echo ' | ';

            echo $v->comment . ' |' . "\r\n";
        }

        exit;
    }
}
