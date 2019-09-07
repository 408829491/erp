<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_order_detail".
 *
 * @property int $id ID
 * @property int $store_id 店铺id
 * @property int $order_id 订单ID
 * @property string $shop_cart_id 购物车id
 * @property int $source_type_id 来源typeID(0普通商品，1促销商品)
 * @property int $commodity_id 商品ID(也包括促销商品id)
 * @property string $commodity_name 商品名称
 * @property int $type_first_tier_id 一级分类Id
 * @property int $type_id 分类Id
 * @property string $parent_type_name 一级分类名称
 * @property string $type_name 商品分类
 * @property int $num 订购数量
 * @property string $pic 商品图片
 * @property string $unit 单位
 * @property int $is_basics_unit 是否基础单位
 * @property int $base_self_ratio 置换比例
 * @property string $basic_unit 基础单位
 * @property string $notice 商品描述
 * @property string $price 单价
 * @property string $total_price 小计
 * @property int $is_delete 是否删除
 * @property string $remark 备注
 * @property int $refund_num 退货数量
 * @property int $is_purchase_num 在途库存
 * @property int $is_seckill 是否促销
 * @property int $is_sorted 是否分拣
 * @property int $actual_num 实际分拣数量
 * @property int $cart_id 购物车ID
 * @property int $update_at 列新时间
 * @property int $create_time 创建时间
 * @property int $stock_position 库位
 * @property int $sort_id 分拣员ID
 * @property string $sort_name 分拣员
 * @property int $sort_time 分拣时间
 */
class CusOrderDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_order_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'order_id', 'shop_cart_id', 'source_type_id', 'is_basics_unit', 'base_self_ratio', 'is_delete', 'refund_num', 'is_purchase_num', 'is_seckill', 'is_sorted', 'actual_num', 'cart_id', 'update_at', 'create_time', 'stock_position', 'sort_id', 'sort_time'], 'integer'],
            [['unit'], 'required'],
            [['notice'], 'string'],
            [['price','total_profit','in_price','num','commodity_id','type_first_tier_id', 'type_id'], 'number'],
            [['commodity_name'], 'string', 'max' => 100],
            [['parent_type_name', 'type_name', 'sort_name'], 'string', 'max' => 50],
            [['pic', 'basic_unit', 'remark'], 'string', 'max' => 300],
            [['unit'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'order_id' => 'Order ID',
            'shop_cart_id' => 'Shop Cart ID',
            'source_type_id' => 'Source Type ID',
            'commodity_id' => 'Commodity ID',
            'commodity_name' => 'Commodity Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'parent_type_name' => 'Parent Type Name',
            'type_name' => 'Type Name',
            'num' => 'Num',
            'pic' => 'Pic',
            'unit' => 'Unit',
            'is_basics_unit' => 'Is Basics Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'basic_unit' => 'Basic Unit',
            'notice' => 'Notice',
            'price' => 'Price',
            'total_price' => 'Total Price',
            'is_delete' => 'Is Delete',
            'remark' => 'Remark',
            'refund_num' => 'Refund Num',
            'is_purchase_num' => 'Is Purchase Num',
            'is_seckill' => 'Is Seckill',
            'is_sorted' => 'Is Sorted',
            'actual_num' => 'Actual Num',
            'cart_id' => 'Cart ID',
            'update_at' => 'Update At',
            'create_time' => 'Create Time',
            'stock_position' => 'Stock Position',
            'sort_id' => 'Sort ID',
            'sort_name' => 'Sort Name',
            'sort_time' => 'Sort Time',
        ];
    }
}
