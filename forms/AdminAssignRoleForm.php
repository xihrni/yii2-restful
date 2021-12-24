<?php

namespace app\forms;

use Yii;
use yii\db\Exception;
use app\models\AdminAuthRole;
use app\models\AdminAuthAssign;
use Throwable;

/**
 * 管理员分配角色表单
 *
 * Class AdminAssignRoleForm
 * @package app\forms
 */
class AdminAssignRoleForm extends \app\base\BaseModel
{
    /**
     * @var int $admin_id 管理员ID
     */
    public $admin_id;

    /**
     * @var array $roles_id 角色ID集合
     */
    public $roles_id;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // 必填
            [['admin_id', 'roles_id'], 'required'],

            // 类型
            [['admin_id'], 'integer', 'min' => 0],
            [['roles_id'], 'each', 'rule' => ['integer']],
            [['roles_id'], 'each', 'rule' => [
                'exist', 'skipOnError' => true, 'targetClass' => AdminAuthRole::className(),
                'targetAttribute' => ['roles_id' => 'id'],
            ]],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => Yii::t('app', '管理员ID'),
            'roles_id' => Yii::t('app', '角色ID集合'),
        ];
    }

    /**
     * 提交
     *
     * @return array|bool 成功或模型错误
     * @throws Throwable
     */
    public function submit()
    {
        if (!$this->validate()) {
            return $this->errors;
        }

        return Yii::$app->db->transaction(function ($e) {
            $this->_deleteAssign();
            $this->_batchCreateAssign();

            // 删除缓存
            Yii::$app->cache->delete('admin:' . $this->admin_id . ':permissions');

            if (!$this->hasErrors()) {
                return true;
            } else {
                $e->transaction->rollBack();
                return $this->errors;
            }
        });
    }


    /* ----private---- */

    /**
     * 删除分配
     *
     * @private
     * @return int
     */
    private function _deleteAssign()
    {
        return AdminAuthAssign::deleteAll(['admin_id' => $this->admin_id]);
    }

    /**
     * 批量创建分配
     *
     * @private
     * @return void|bool
     * @throws Exception
     */
    private function _batchCreateAssign()
    {
        $data = array_map(function ($v) {
            return [
                'admin_id' => $this->admin_id,
                'role_id'  => $v,
            ];
        }, $this->roles_id);

        $affect = Yii::$app->db->createCommand()
            ->batchInsert(AdminAuthAssign::tableName(), ['admin_id', 'role_id'], $data)
            ->execute();

        if ($affect != count($data)) {
            $this->addError('batch_create_assign', Yii::t('app/error', 'Batch creation failed.'));
            return false;
        }
    }
}
