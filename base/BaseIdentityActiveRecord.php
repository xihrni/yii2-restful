<?php

namespace app\base;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * 基础身份活跃记录类
 *
 * Class BaseIdentityActiveRecord
 * @package app\base
 */
class BaseIdentityActiveRecord extends BaseActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @var string [DEFAULT_PASSWORD = '123456'] 默认密码
     */
    const DEFAULT_PASSWORD = '123456';


    /**
     * 获取ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * 获取认证秘钥
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * 设置密码
     *
     * @param  string $password 密码
     * @return void
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 验证密码
     *
     * @param  string $password 密码
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * 验证认证秘钥
     *
     * @param  string $authKey 认证秘钥
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 查询身份
     *
     * @param  int $id 主键
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_trash' => 0, 'status' => 1]);
    }

    /**
     * 查询身份根据用户名
     *
     * @param  string $username 用户名
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByUsername($username)
    {
        return static::findOne(['username' => $username, 'is_trash' => 0, 'status' => 1]);
    }

    /**
     * 查询身份根据手机号码
     *
     * @param  string  $mobile       手机号码
     * @param  integer [$status = 1] 状态
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByMobile($mobile, $status = 1)
    {
        $where = ['mobile' => $mobile, 'is_trash' => 0];
        if ($status !== null) {
            $where['status'] = $status;
        }

        return static::findOne($where);
    }

    /**
     * 查询身份根据微信openid
     *
     * @param  string $wechatOpenid 微信openid
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByWechatOpenid($wechatOpenid)
    {
        return static::findOne(['wechat_openid' => $wechatOpenid, 'is_trash' => 0, 'status' => 1]);
    }

    /**
     * 查询身份根据访问令牌
     *
     * @param  string $authKey 认证密钥
     * @param  string $userIp  用户IP
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByAuthKey($authKey, $userIp)
    {
        return static::findOne([
            'auth_key'      => $authKey,
            'is_trash'      => 0,
            'status'        => 1,
            // 'last_login_at' => ['>', time() - 60 * 5],
            'last_login_ip' => $userIp,
        ]);
    }

    /**
     * 查询身份根据访问令牌
     *
     * @param  string $accessToken   访问令牌
     * @param  null   [$type = null] 类型
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByAccessToken($accessToken, $type = null)
    {
        $model = static::findOne([
            'access_token'  => $accessToken,
            'is_trash'      => 0,
            'status'        => 1,
        ]);

        if ($model && strtotime($model->last_login_at) < time() - Yii::$app->params['accessTokenPeriod']) {
            return null;
        }

        return $model;
    }

    /**
     * 查询身份根据重置密码令牌
     *
     * @param  string $passwordResetToken 重置密码令牌
     * @return null|ActiveRecord|IdentityInterface
     */
    public static function findIdentityByPasswordResetToken($passwordResetToken)
    {
        return static::findOne(['password_reset_token' => $passwordResetToken, 'is_trash' => 0, 'status' => 1]);
    }

    /**
     * 生成重置密码令牌
     *
     * @return void
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString(52) . '_' . time();
    }

    /**
     * 生成认证密钥
     *
     * @return void
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(52) . '_' . time();
    }

    /**
     * 生成访问令牌
     *
     * @return void
     * @throws Exception
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString(52) . '_' . time();
    }

    /**
     * 清除重置密码令牌
     *
     * @return void
     */
    public function clearPasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * 清除重置密码令牌
     *
     * @return void
     */
    public function clearAuthKey()
    {
        $this->auth_key = null;
    }

    /**
     * 清除访问令牌
     *
     * @return void
     */
    public function clearAccessToken()
    {
        $this->access_token = null;
    }
}
