<?php

namespace app\base;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use xihrni\yii2\behaviors\TimeBehavior;
use xihrni\tools\Verify;

/**
 * 基础活跃记录类
 *
 * Class BaseActiveRecord
 * @package app\base
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * 行为
     *
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'typecast' => [
                'class' => AttributeTypecastBehavior::className(),
                'typecastAfterValidate' => true,
                'typecastBeforeSave' => true,
                'typecastAfterFind' => true,
            ],
            'time' => [
                'class' => TimeBehavior::className(),
            ],
        ]);
    }

    /**
     * 软删除
     *
     * @return bool
     */
    public function softDelete()
    {
        $this->is_trash   = 1;
        $this->deleted_at = date('Y-m-d H:i:s');

        return $this->save(true, ['is_trash', 'deleted_at']);
    }

    /**
     * 构造查询集合语句
     *
     * @public
     * @param  string $needle   需要查找的字符
     * @param  string $haystack 被查找的字段
     * @return string
     */
    public static function buildFindInSet($needle, $haystack)
    {
        return "FIND_IN_SET({$needle}, (SELECT TRIM(BOTH '[' FROM TRIM(BOTH ']' FROM $haystack))))";
    }


    /* ----validate---- */

    /**
     * 验证身份证号码
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return bool|void
     */
    public function validateIdcard($attribute, $params)
    {
        if (!$this->hasErrors() && !Verify::idCardNumber($this->$attribute)) {
            $this->addErrorForInvalid($attribute);
            return false;
        }
    }

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

    /**
     * 验证关联ID集合
     *
     * ```
     * [['user_ids'], 'validateRelationIds', 'params' => ['user_ids' => User::className()]]
     * ```
     *
     * @param  string $attribute 当前正在验证的属性
     * @param  array  $params    规则中给出的其他键值对
     * @return bool|void
     */
    public function validateRelationIds($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // 去重
            $this->$attribute = array_unique($this->$attribute);
            $modelClass = $params[$attribute];

            // 判断数量是否一致
            $count = $modelClass::find()->where(['id' => $this->$attribute, 'is_trash' => 0])->count('id');
            if ($count != count($this->$attribute)) {
                $this->addErrorForInvalid($attribute);
                return false;
            }
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
