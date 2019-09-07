<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_seckill_commodity".
 *
 * @property int $id ID
 * @property int $cus_commodity_id 商品ID
 * @property int $cus_seckill_id 秒杀表ID
 * @property string $store_id 门店id
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
 * @property int $sell_num 销量
 * @property int $sequence 排序
 * @property int $provider_id 供应商ID
 * @property string $commodity_code 产品编码
 * @property string $is_rough 是否标品
 * @property string $pinyin 商品助记码
 * @property int $agent_id 采购员ID
 * @property int $parent_id 推荐人ID
 * @property string $alias 商品别名
 * @property int $channel_type 采购类型(0自采,1供应商供货)
 * @property string $tag 商品标签
 * @property int $is_time_price 是否时价
 * @property string $brand 商品品牌
 * @property string $product_place 商品产地
 * @property string $loss_rate 损耗率
 * @property string $durability_period 保质期
 * @property string $sell_stock 售卖库存
 * @property int $is_sell_stock 是否限制卖库存
 * @property int $unit_change_disabled 禁止修改单位
 * @property string $notice 详情描述
 * @property string $pic 图片
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusSeckillCommodity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_seckill_commodity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cus_commodity_id', 'name'], 'required'],
            [['cus_commodity_id', 'cus_seckill_id', 'store_id', 'type_first_tier_id', 'type_id', 'limit_buy', 'is_online', 'sell_num', 'sequence', 'provider_id', 'agent_id', 'parent_id', 'channel_type', 'is_time_price', 'is_sell_stock', 'unit_change_disabled'], 'integer'],
            [['price', 'in_price', 'activity_price', 'loss_rate', 'sell_stock'], 'number'],
            [['is_rough', 'pic'], 'string'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name', 'tag', 'notice'], 'string', 'max' => 255],
            [['unit', 'commodity_code', 'pinyin', 'brand', 'durability_period'], 'string', 'max' => 50],
            [['summary'], 'string', 'max' => 999],
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
            'cus_commodity_id' => 'Cus Commodity ID',
            'cus_seckill_id' => 'Cus Seckill ID',
            'store_id' => 'Store ID',
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
            'sell_num' => 'Sell Num',
            'sequence' => 'Sequence',
            'provider_id' => 'Provider ID',
            'commodity_code' => 'Commodity Code',
            'is_rough' => 'Is Rough',
            'pinyin' => 'Pinyin',
            'agent_id' => 'Agent ID',
            'parent_id' => 'Parent ID',
            'alias' => 'Alias',
            'channel_type' => 'Channel Type',
            'tag' => 'Tag',
            'is_time_price' => 'Is Time Price',
            'brand' => 'Brand',
            'product_place' => 'Product Place',
            'loss_rate' => 'Loss Rate',
            'durability_period' => 'Durability Period',
            'sell_stock' => 'Sell Stock',
            'is_sell_stock' => 'Is Sell Stock',
            'unit_change_disabled' => 'Unit Change Disabled',
            'notice' => 'Notice',
            'pic' => 'Pic',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
