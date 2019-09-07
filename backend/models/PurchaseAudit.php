<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchase}}".
 *
 * @property int $id ID
 * @property string $pur_no 采购单号
 * @property int $storage_id 仓库ID
 * @property string $storage_name 仓库名称
 * @property string $plan_date 计划交货日期
 * @property int $purchase_type 采购类型：0市场自采，1供应商供货
 * @property string $purchase_status 采购状态描述
 * @property string $purchase_time 计划采购日期
 * @property int $source 单据来源（订单汇总生成采购单，手动创建采购单）
 * @property int $agent_id 采购员ID
 * @property string $agent_name 采购员姓名
 * @property int $provider_id 供应商ID
 * @property string $provider_name 供应商姓名
 * @property int $procured_num 已收种数
 * @property int $purchase_num 采购商品种数
 * @property string $share_url 分享URL
 * @property string $purchase_price 采购总价
 * @property string $audit_price 对账金额
 * @property string $settle_price 对账金额
 * @property string $author 制单人
 * @property int $sort_type 排序类型,1手动新增排序，2，按一二级分类排序
 * @property int $status 状态
 * @property string $remark 备注
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class PurchaseAudit extends \yii\db\ActiveRecord
{
    public $commodity_list = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%purchase_audit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_no','create_time'], 'required'],
            [['storage_id', 'purchase_type', 'source', 'agent_id', 'provider_id', 'procured_num', 'purchase_num', 'sort_type', 'status', 'is_delete', 'create_time','is_settlement','settlement_time','is_audit','audit_time','audit_type'], 'integer'],
            [['settle_price','price','audit_price'], 'number'],
            [['purchase_no', 'storage_name', 'purchase_status', 'agent_name', 'provider_name'], 'string', 'max' => 50],
            [['plan_date', 'purchase_time', 'author'], 'string', 'max' => 30],
            [['share_url', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_no' => '采购单号',
            'storage_id' => '仓库ID',
            'storage_name' => '仓库名称',
            'plan_date' => '计划交货日期',
            'purchase_type' => '采购类型',
            'purchase_status' => '采购状态描述',
            'purchase_time' => '计划采购日期',
            'source' => '单据来源',
            'agent_id' => '采购员ID',
            'agent_name' => '采购员姓名',
            'provider_id' => '供应商ID',
            'provider_name' => '供应商姓名',
            'procured_num' => '已收种数',
            'purchase_num' => '采购商品种数',
            'share_url' => '分享URL',
            'purchase_price' => '采购总价',
            'audit_price' => '对账金额',
            'author' => '制单人',
            'sort_type' => '排序类型',
            'status' => '状态',
            'remark' => '备注',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 关联对账单商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(PurchaseAuditDetail::className(), ['audit_id' => 'id']);
    }
}
