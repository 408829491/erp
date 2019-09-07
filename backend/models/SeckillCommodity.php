<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_seckill_commodity".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property int $seckill_id 秒杀表ID
 * @property string $name 商品名称
 * @property int $type_first_tier_id 商品类型第一层ID(方便查询)
 * @property int $type_id 商品类型ID
 * @property string $price 市场价
 * @property string $in_price 进货价
 * @property string $activity_price 活动价
 * @property int $limit_buy 单人限购
 * @property string $unit 单位
 * @property string $summary 描述
 * @property int $is_online 是否上架
 * @property int $sequence 排序
 * @property string $category 类别组(弃)
 * @property int $category_id 大类(弃)
 * @property int $category_id2 子类(弃)
 * @property string $rule_status 是否标品
 * @property int $order_quantity 起订量
 * @property int $max_quantity 最大订购数理
 * @property int $provider_id 供应商ID
 * @property string $is_active 是否激活
 * @property string $commodity_code 产品编码
 * @property string $is_rough 是否标品
 * @property string $unit_convert 是否转换单位
 * @property string $unit_sell 转换单位
 * @property string $unit_num 单位计量
 * @property string $pinyin 商品助记码
 * @property int $agent_id 采购员ID
 * @property int $parent_id 推荐人ID
 * @property int $is_process
 * @property string $alias 商品别名
 * @property string $status 状态
 * @property int $hide 是否
 * @property int $channel_type 采购类型
 * @property string $tag 商品标签
 * @property int $allow_change_channel
 * @property int $is_time_price 是否时价
 * @property string $brand 商品品牌
 * @property string $product_place 商品产地
 * @property string $loss_rate 损耗率
 * @property string $durability_period 保质期
 * @property string $sell_stock 售卖库存
 * @property int $is_sell_stock 是否限制卖库存
 * @property int $product_line_id 产品线路id
 * @property int $unit_change_disabled 禁止修改单位
 * @property string $notice 详情描述
 * @property string $create_time 创建时间
 * @property string $modify_time 更新时间
 * @property string $pic 图片
 */
class SeckillCommodity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_seckill_commodity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'name'], 'required'],
            [['commodity_id', 'seckill_id', 'type_first_tier_id', 'type_id', 'limit_buy', 'is_online', 'sequence', 'category_id', 'category_id2', 'order_quantity', 'max_quantity', 'provider_id', 'agent_id', 'parent_id', 'is_process', 'hide', 'channel_type', 'allow_change_channel', 'is_time_price', 'is_sell_stock', 'product_line_id', 'unit_change_disabled'], 'integer'],
            [['price', 'in_price', 'activity_price', 'unit_num', 'loss_rate', 'sell_stock'], 'number'],
            [['rule_status', 'is_active', 'is_rough', 'unit_convert', 'status', 'pic'], 'string'],
            [['create_time', 'modify_time'], 'safe'],
            [['name', 'summary', 'category', 'tag', 'notice'], 'string', 'max' => 255],
            [['unit', 'commodity_code', 'pinyin', 'brand', 'durability_period'], 'string', 'max' => 50],
            [['unit_sell'], 'string', 'max' => 10],
            [['alias'], 'string', 'max' => 100],
            [['product_place'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commodity_id' => 'Commodity ID',
            'seckill_id' => 'Seckill ID',
            'name' => 'Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'price' => 'Price',
            'in_price' => 'In Price',
            'activity_price' => 'Activity Price',
            'limit_buy' => 'Limit Buy',
            'unit' => 'Unit',
            'summary' => 'Summary',
            'is_online' => 'Is Online',
            'sequence' => 'Sequence',
            'category' => 'Category',
            'category_id' => 'Category ID',
            'category_id2' => 'Category Id2',
            'rule_status' => 'Rule Status',
            'order_quantity' => 'Order Quantity',
            'max_quantity' => 'Max Quantity',
            'provider_id' => 'Provider ID',
            'is_active' => 'Is Active',
            'commodity_code' => 'Commodity Code',
            'is_rough' => 'Is Rough',
            'unit_convert' => 'Unit Convert',
            'unit_sell' => 'Unit Sell',
            'unit_num' => 'Unit Num',
            'pinyin' => 'Pinyin',
            'agent_id' => 'Agent ID',
            'parent_id' => 'Parent ID',
            'is_process' => 'Is Process',
            'alias' => 'Alias',
            'status' => 'Status',
            'hide' => 'Hide',
            'channel_type' => 'Channel Type',
            'tag' => 'Tag',
            'allow_change_channel' => 'Allow Change Channel',
            'is_time_price' => 'Is Time Price',
            'brand' => 'Brand',
            'product_place' => 'Product Place',
            'loss_rate' => 'Loss Rate',
            'durability_period' => 'Durability Period',
            'sell_stock' => 'Sell Stock',
            'is_sell_stock' => 'Is Sell Stock',
            'product_line_id' => 'Product Line ID',
            'unit_change_disabled' => 'Unit Change Disabled',
            'notice' => 'Notice',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'pic' => 'Pic',
        ];
    }
}
