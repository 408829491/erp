<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%cus_group_order_member}}".
 *
 * @property int $id ID
 * @property int $cus_group_order_id 团购订单id
 * @property int $is_group_commander 是否团长
 * @property int $user_id 用户ID
 * @property string $nickname 昵称
 * @property string $head_pic 用户头像
 */
class CusGroupOrderMember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cus_group_order_member}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cus_group_order_id', 'is_group_commander', 'user_id'], 'integer'],
            [['nickname'], 'string', 'max' => 32],
            [['head_pic'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cus_group_order_id' => '团购订单id',
            'is_group_commander' => '是否团长',
            'user_id' => '用户ID',
            'nickname' => '昵称',
            'head_pic' => '用户头像',
        ];
    }
}
