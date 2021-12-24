<?php

namespace app\modules\v1\admin\modules\admin\controllers;

use yii\db\ActiveRecord;
use xihrni\tools\Arrays;

/**
 * 菜单管理控制器
 *
 * Class MenuController
 * @package app\modules\v1\admin\modules\admin\controllers
 */
class MenuController extends \app\modules\v1\admin\controllers\CommonController
{
    /**
     * @var string $modelClass 模型类文件
     */
    public $modelClass = 'app\modules\v1\admin\resources\AdminAuthMenuResource';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['query'] = function ($model, $params) {
            $where    = ['is_trash' => 0];
            $parentId = $params['parentId'] ?? 0; // 父ID，0=>全部，null=>顶级，其他=>为下级

            if ($parentId === 'null') {
                $where['parent_id'] = null;
            } else if ($parentId !== 0) {
                $where['parent_id'] = $parentId;
            }

            /* @var $model ActiveRecord */
            return ($model::find())->where($where);
        };
        $actions['index']['sort'] = ['sort' => SORT_DESC, 'id' => SORT_DESC];

        return $actions;
    }

    /**
     * 树
     *
     * @return array
     */
    public function actionTree()
    {
        /* @var $model ActiveRecord */
        $model  = $this->modelClass;
        $result = $model::find()->select([
            'id as value',
            'parent_id',
            'name as label',
            'sort',
            'status',
            'created_at',
            'updated_at',
        ])->where(['is_trash' => 0])->orderBy(['sort' => SORT_DESC, 'id' => SORT_ASC])->asArray()->all();

        return Arrays::list2Tree($result, 'value');
    }
}
