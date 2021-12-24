<?php

namespace app\base;

use Yii;

/**
 * 基础模型类
 *
 * Class BaseModel
 * @package app\base
 */
class BaseModel extends \yii\base\Model
{
    /* ----validate---- */

    /**
     * 验证数组
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return bool|void
     */
    public function validateArray($attribute, $params)
    {
        if (!$this->hasErrors() && !is_array($this->$attribute)) {
            $this->addErrorForInvalid($attribute);
            return false;
        }
    }


    /* ----other---- */

    /**
     * 向指定属性添加新无效错误
     *
     * @param  $attribute
     * @return void
     */
    public function addErrorForInvalid($attribute)
    {
        $this->addError($attribute, Yii::t('app/error', '{attribute} is invalid.', [
            'attribute' => $this->getAttributeLabel($attribute),
        ]));
    }
}
