<?php

namespace app\modules\v1\admin\resources;

use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use app\models\AdminAuthMenu;

/**
 * 管理员权限资源
 *
 * Class AdminAuthPermissionResource
 * @package app\modules\v1\admin\resources
 */
class AdminAuthPermissionResource extends \app\models\AdminAuthPermission
{
    /**
     * @var array $_menus 菜单
     */
    public static $_menus;


    /**
     * 字段
     *
     * @return array
     */
    public function fields()
    {
        return [
            'id',
            'menus' => function ($model) {
                return array_reverse($this->_getSelectedMenus($model->menu_id));
            },
            'title',
            'modules',
            'controller',
            'action',
            'name',
            'method',
            'condition' => function ($model) {
                return $model->condition ? json_decode($model->condition, true) : '';
            },
            'sort',
            'status',
            'created_at',
            'updated_at',
        ];
    }


    /* ----private---- */

    /**
     * 获取菜单
     *
     * @protected
     * @return array
     */
    protected function getMenus()
    {
        if (!static::$_menus) {
            // 缓存依赖
            $dependency = new DbDependency([
                'sql' => 'SELECT MAX(updated_at) FROM ' . AdminAuthMenu::tableName() . ' WHERE is_trash = 0',
            ]);

            $menus = AdminAuthMenu::find()
                ->select(['id', 'parent_id', 'name'])
                ->where(['is_trash' => 0])
                ->cache(true, $dependency)
                ->asArray()
                ->all();

            static::$_menus = ArrayHelper::index($menus, 'id');
        }

        return static::$_menus;
    }

    /**
     * 获取选中的菜单
     *
     * @private
     * @param  int   $menuId        菜单ID
     * @param  array [$result = []] 存放选中的菜单
     * @return array
     */
    private function _getSelectedMenus(int $menuId, &$result = [])
    {
        $selected = $this->menus[$menuId];
        $result[] = $selected;

        if ($selected['parent_id']) {
            $this->_getSelectedMenus($selected['parent_id'], $result);
        }

        return $result;
    }
}
