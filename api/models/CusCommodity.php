<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_commodity".
 *
 * @property int $id ID
 * @property string $store_id 门店id
 * @property string $uid 门店商品id
 * @property string $name 商品名称
 * @property int $type_first_tier_id 商品类型第一层ID(方便查询)
 * @property int $type_id 商品类型ID
 * @property string $price 市场价
 * @property string $in_price 进货价
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
class CusCommodity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_commodity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'is_online', 'sell_num', 'sequence', 'provider_id', 'agent_id', 'parent_id', 'channel_type', 'is_time_price', 'is_sell_stock', 'unit_change_disabled'], 'integer'],
            [['name', 'type_id', 'commodity_code'], 'required'],
            [['price', 'in_price', 'loss_rate', 'sell_stock','type_id','type_first_tier_id','uid'], 'number'],
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
            'store_id' => '门店id',
            'name' => '商品名称',
            'type_first_tier_id' => '商品类型第一层ID(方便查询)',
            'type_id' => '商品类型ID',
            'price' => '市场价',
            'in_price' => '进货价',
            'unit' => '单位',
            'summary' => '描述',
            'is_online' => '是否上架',
            'sell_num' => '销量',
            'sequence' => '排序',
            'provider_id' => '供应商ID',
            'commodity_code' => '产品编码',
            'is_rough' => '是否标品',
            'pinyin' => '商品助记码',
            'agent_id' => '采购员ID',
            'parent_id' => '推荐人ID',
            'alias' => '商品别名',
            'channel_type' => '采购类型(0自采,1供应商供货)',
            'tag' => '商品标签',
            'is_time_price' => '是否时价',
            'brand' => '商品品牌',
            'product_place' => '商品产地',
            'loss_rate' => '损耗率',
            'durability_period' => '保质期',
            'sell_stock' => '售卖库存',
            'is_sell_stock' => '是否限制卖库存',
            'unit_change_disabled' => '禁止修改单位',
            'notice' => '详情描述',
            'pic' => '图片',
            'create_datetime' => '创建日期时间',
            'modify_datetime' => '修改日期时间',
        ];
    }
}
