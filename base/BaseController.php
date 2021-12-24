<?php

namespace app\base;

use Yii;
use yii\web\Cookie;

/**
 * 基础控制器类

 * Class BaseController
 * @package app\base
 */
class BaseController extends \yii\web\Controller
{
    /**
     * 获取 cookie
     *
     * @param  string $name                  名称
     * @param  mixed  [$defaultValue = null] 不存在时的默认值
     * @return mixed
     */
    public function getCookie($name, $defaultValue = null)
    {
        return Yii::$app->request->cookies->getValue($name, $defaultValue);
    }

    /**
     * 设置 cookie
     *
     * @param  string $name              名称
     * @param  mixed  $value             值
     * @param  string [$domain = '']     域
     * @param  int    [$expire = 0]      超时时间
     * @param  string [$path = '/']      路径
     * @param  bool   [$secure = false]  安全链接
     * @param  bool   [$httpOnly = true] 只能通过HTTP协议访问
     * @return void
     */
    public function setCookie($name, $value, $domain = '', $expire = 0, $path = '/', $secure = false, $httpOnly = true)
    {
        Yii::$app->response->cookies->add(new Cookie([
            'name'     => $name,
            'value'    => $value,
            'domain'   => $domain,
            'expire'   => $expire,
            'path'     => $path,
            'secure'   => $secure,
            'httpOnly' => $httpOnly,
        ]));
    }

    /**
     * 是否有 cookie
     *
     * @param  string $name 名称
     * @return bool
     */
    public function hasCooke($name)
    {
        return Yii::$app->request->cookies->has($name);
    }

    /**
     * 移除 cookie
     *
     * @param  Cookie|string $cookie     Cookie对象或名称
     * @param  bool [$removeFromBrowser] 是否从浏览器中删除cookie
     * @return void
     */
    public function removeCookie($cookie, $removeFromBrowser = true)
    {
        Yii::$app->request->cookies->remove($cookie, $removeFromBrowser);
    }
}
