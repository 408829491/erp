<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_order_detail".
 *
 * @property int $id ID
 * @property int $order_id 订单ID
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property int $type_first_tier_id 一级分类Id
 * @property int $type_id 分类Id
 * @property string $parent_type_name 一级分类名称
 * @property string $type_name 商品分类
 * @property int $num 订购数量
 * @property string $pic 商品图片
 * @property string $unit 单位
 * @property string $notice 商品描述
 * @property string $in_price 成本价
 * @property string $price 单价
 * @property int $channel_type 采购类型
 * @property string $total_profit 利润
 * @property string $total_price 小计
 * @property int $is_delete 是否删除
 * @property string $remark 备注
 * @property int $refund_num 退货数量
 * @property int $is_purchase_num 在途库存
 * @property int $is_seckill 是否促销
 * @property int $is_sorted 是否分拣
 * @property int $actual_num 实际分拣数量
 * @property string $delivery_date 发货日期
 * @property int $update_at 列新时间
 * @property int $create_time 创建时间
 * @property int $stock_position 库位
 * @property int $sort_id 分拣员ID
 * @property int $is_basics_unit 是否基础单位
 * @property string $base_unit 基础单位
 * @property double $base_self_ratio 换算比率
 * @property string $sort_name 分拣员
 * @property int $sort_time 分拣时间
 * @property int $provider_id 供应商ID
 * @property string $provider_name 供应商名称
 * @property int $agent_id 采购员ID
 * @property string $agent_name 采购员姓名
 */
class OrderDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_order_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'commodity_id', 'type_first_tier_id', 'type_id', 'num', 'channel_type', 'is_delete', 'refund_num', 'is_purchase_num', 'is_seckill', 'is_sorted', 'actual_num', 'update_at', 'create_time', 'stock_position', 'sort_id', 'is_basics_unit', 'sort_time', 'provider_id', 'agent_id','c_type'], 'integer'],
            [['unit'], 'required'],
            [['notice'], 'string'],
            [['in_price', 'price', 'total_profit', 'total_price', 'base_self_ratio'], 'number'],
            [['commodity_name'], 'string', 'max' => 100],
            [['parent_type_name', 'type_name', 'sort_name', 'provider_name', 'agent_name'], 'string', 'max' => 50],
            [['pic', 'remark'], 'string', 'max' => 300],
            [['unit'], 'string', 'max' => 20],
            [['delivery_date', 'base_unit'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'commodity_id' => 'Commodity ID',
            'commodity_name' => 'Commodity Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'parent_type_name' => 'Parent Type Name',
            'type_name' => 'Type Name',
            'num' => 'Num',
            'pic' => 'Pic',
            'unit' => 'Unit',
            'notice' => 'Notice',
            'in_price' => 'In Price',
            'price' => 'Price',
            'channel_type' => 'Channel Type',
            'total_profit' => 'Total Profit',
            'total_price' => 'Total Price',
            'is_delete' => 'Is Delete',
            'remark' => 'Remark',
            'refund_num' => 'Refund Num',
            'is_purchase_num' => 'Is Purchase Num',
            'is_seckill' => 'Is Seckill',
            'is_sorted' => 'Is Sorted',
            'actual_num' => 'Actual Num',
            'delivery_date' => 'Delivery Date',
            'update_at' => 'Update At',
            'create_time' => 'Create Time',
            'stock_position' => 'Stock Position',
            'sort_id' => 'Sort ID',
            'is_basics_unit' => 'Is Basics Unit',
            'base_unit' => 'Base Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'sort_name' => 'Sort Name',
            'sort_time' => 'Sort Time',
            'provider_id' => 'Provider ID',
            'provider_name' => 'Provider Name',
            'agent_id' => 'Agent ID',
            'agent_name' => 'Agent Name',
        ];
    }
}
