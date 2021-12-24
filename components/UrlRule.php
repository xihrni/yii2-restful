<?php

namespace app\components;

/**
 * URL 规则（继承 \yii\rest\UrlRule 重写创建规则方法处理控制器中的模块名称小驼峰转换用 '-' 方式访问）
 *
 * Class UrlRule
 * @package app\components
 */
class UrlRule extends \yii\rest\UrlRule
{
    /**
     * {@inheritdoc}
     */
    public function createRules()
    {
        $key = array_keys($this->controller)[0];
        $controller = array_values($this->controller)[0];

        $minus = '-'; // 减号
        $slash = '/'; // 反斜杠
        $array = explode($slash, $controller); // 拆分控制器
        if (count($array) > 1) {
            $lastController   = $slash . array_pop($array); // 最后一个控制器无需转换
            $controller       = implode($slash, $array);
            $controller       = $minus . str_replace($minus, ' ', strtolower($controller));
            $controller       = ltrim(str_replace(' ', '', ucwords($controller)), $minus) . $lastController;
            $this->controller = [$key => $controller];

        }

        return parent::createRules();
    }
}
