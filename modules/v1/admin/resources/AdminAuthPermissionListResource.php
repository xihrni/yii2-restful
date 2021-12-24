<?php

namespace app\modules\v1\admin\resources;

use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use app\models\AdminAuthMenu;

/**
 * 管理员权限列表资源
 *
 * Class AdminAuthPermissionListResource
 * @package app\modules\v1\admin\resources
 */
class AdminAuthPermissionListResource extends \app\models\AdminAuthPermission
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
            'menu' => function ($model) {
                $rootId = $this->_getMenuRootId($model->menu_id);

                return [
                    'id' => $model->menu_id,
                    'name' => $this->menus[$model->menu_id]['name'] ? $this->menus[$model->menu_id]['name'] : '',
                    'root_id' => $rootId,
                    'root_name' => $this->menus[$rootId]['name'] ? $this->menus[$rootId]['name'] : '',
                ];
            },
            'title',
        ];
    }


    /* ----private---- */

    /**
     * 获取菜单
     *
     * @protected
     * @return mixed
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
     * 获取顶级菜单ID
     *
     * @private
     * @param  int $menuId 菜单ID
     * @return int
     */
    private function _getMenuRootId($menuId)
    {
        if (!$this->menus[$menuId]['parent_id']) {
            return $this->menus[$menuId]['id'];
        } else {
            return $this->_getMenuRootId($this->menus[$menuId]['parent_id']);
        }
    }
}
