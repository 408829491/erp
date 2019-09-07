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
 * @property int $num 订购数量
 * @property string $pic 商品图片
 * @property string $unit 单位
 * @property string $notice 商品描述
 * @property string $price 单价
 * @property string $total_price 小计
 * @property int $is_delete 是否删除
 * @property string $remark 备注
 * @property int $create_time 创建时间
 * @property int $stock_position 库位
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
            [['order_id', 'commodity_id', 'num', 'refund_num','is_delete', 'create_time','is_seckill','type_id','type_first_tier_id','is_sorted','stock_position','sort_id','sort_time'], 'integer'],
            [['unit', 'price','order_id','commodity_id'], 'required'],
            [['notice'], 'string'],
            [['price', 'total_price'], 'number'],
            [['commodity_name','sort_name'], 'string', 'max' => 100],
            [['pic', 'remark','type_name','parent_type_name'], 'string', 'max' => 300],
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
            'order_id' => '订单ID',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'type_name' => '商品分类',
            'num' => '订购数量',
            'refund_num' => '已退数量',
            'pic' => '商品图片',
            'unit' => '单位',
            'notice' => '商品描述',
            'price' => '单价',
            'total_price' => '小计',
            'is_delete' => '是否删除',
            'is_seckill' => '是否秒杀',
            'is_sorted' => '是否分拣',
            'remark' => '备注',
            'create_time' => '创建时间',
        ];
    }

    /**
     * {@inheritdoc}
     * @return OrderDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderDetailQuery(get_called_class());
    }
}
