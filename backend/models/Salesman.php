<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%salesman}}".
 *
 * @property int $id ID
 * @property string $name 姓名
 * @property string $account_tel 电话/登录帐号
 * @property string $password 密码
 * @property string $invitation_code 邀请码
 * @property int $lock_status 0正常/1冻结
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class Salesman extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%salesman}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lock_status', 'is_delete', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 80],
            [['account_tel'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 100],
            [['invitation_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '姓名',
            'account_tel' => '电话/登录帐号',
            'password' => '密码',
            'invitation_code' => '邀请码',
            'lock_status' => '0正常/1冻结',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
