<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%order_refund}}".
 *
 * @property int $id ID
 * @property string $refund_no 退款单号
 * @property int $order_id 订单ID
 * @property string $order_no 订单号
 * @property int $status 状态码：0提交，100待审核，2待入库，3待退款，4已完成，5已关闭
 * @property string $remark 退货/退货原因
 * @property int $user_id 客户ID
 * @property string $pic 退款图片
 * @property string $user_name 客户名称
 * @property string $stock_in_no 入库单号
 * @property int $stock_in_id 入库单ID
 * @property string $apply_price 申请退款金额
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class OrderRefund extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_refund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refund_no', 'order_id', 'remark', 'user_id', 'user_name'], 'required'],
            [['order_id', 'status', 'user_id', 'stock_in_id', 'is_delete', 'create_time'], 'integer'],
            [['pic'], 'string'],
            [['apply_price'], 'number'],
            [['refund_no'], 'string', 'max' => 255],
            [['order_no', 'remark', 'user_name', 'stock_in_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refund_no' => '退款单号',
            'order_id' => '订单ID',
            'order_no' => '订单号',
            'status' => '状态码',
            'remark' => '退货/退货原因',
            'user_id' => '客户ID',
            'pic' => '退款图片',
            'user_name' => '客户名称',
            'stock_in_no' => '入库单号',
            'stock_in_id' => '入库单ID',
            'apply_price' => '申请退款金额',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
