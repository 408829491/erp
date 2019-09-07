<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchase_refund}}".
 *
 * @property int $id ID
 * @property string $refund_no 退货单号
 * @property string $purchase_no 采购单号
 * @property int $storage_id 仓库ID
 * @property string $storage_name 仓库名称
 * @property int $purchase_type 采购类型：0市场自采，1供应商供货
 * @property string $purchase_status 采购状态描述
 * @property int $agent_id 采购员ID
 * @property string $agent_name 采购员姓名
 * @property int $refund_num 退货数量
 * @property string $price 退货金额
 * @property string $author 制单人
 * @property int $status 状态
 * @property int $is_settlement 是否结算
 * @property int $settlement_time 结算时间
 * @property int $is_audit 是否对账
 * @property int $audit_time 对账时间
 * @property string $remark 备注
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class PurchaseRefund extends \yii\db\ActiveRecord
{
    public $commodity_list = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%purchase_refund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refund_no'], 'required'],
            [['storage_id', 'purchase_type', 'agent_id', 'refund_num', 'status', 'is_settlement', 'settlement_time', 'is_audit', 'audit_time', 'is_delete', 'create_time'], 'integer'],
            [['price'], 'number'],
            [['refund_no', 'purchase_no', 'storage_name', 'purchase_status', 'agent_name'], 'string', 'max' => 50],
            [['author'], 'string', 'max' => 30],
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
            'refund_no' => '退货单号',
            'purchase_no' => '采购单号',
            'storage_id' => '仓库ID',
            'storage_name' => '仓库名称',
            'purchase_type' => '采购类型：0市场自采，1供应商供货',
            'purchase_status' => '采购状态描述',
            'agent_id' => '采购员ID',
            'agent_name' => '采购员姓名',
            'refund_num' => '退货数量',
            'price' => '退货金额',
            'author' => '制单人',
            'status' => '状态',
            'is_settlement' => '是否结算',
            'settlement_time' => '结算时间',
            'is_audit' => '是否对账',
            'audit_time' => '对账时间',
            'remark' => '备注',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 关联采购单商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(PurchaseRefundDetail::className(), ['refund_id' => 'id']);
    }
}
