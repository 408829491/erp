<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_finance_balance".
 *
 * @property int $id ID
 * @property string $recharge_no 流水单号
 * @property int $type 类型0充值1扣款2订单支付
 * @property int $user_id 用户id
 * @property string $user_name 用户名
 * @property string $op_user 操作员
 * @property string $amount 金额
 * @property string $current_balance 当前余额
 * @property string $pay_user 交款人
 * @property string $remark 备注
 * @property string $tel 电话
 * @property string $refer_no 关联单号
 * @property int $create_time 创建时间
 */
class FinanceBalance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_finance_balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['recharge_no'], 'required'],
            [['type', 'user_id', 'create_time'], 'integer'],
            [['amount', 'current_balance'], 'number'],
            [['recharge_no', 'user_name', 'op_user', 'pay_user', 'tel', 'refer_no'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recharge_no' => '流水单号',
            'type' => '类型0充值1扣款2订单支付',
            'user_id' => '用户id',
            'user_name' => '用户名',
            'op_user' => '操作员',
            'amount' => '金额',
            'current_balance' => '当前余额',
            'pay_user' => '交款人',
            'remark' => '备注',
            'tel' => '电话',
            'refer_no' => '关联单号',
            'create_time' => '创建时间',
        ];
    }
}
