<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_in_detail}}".
 *
 * @property int $id ID
 * @property int $in_id 入库ID
 * @property string $in_no 入库单号
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property string $pic 商品图片
 * @property string $price 入库价格
 * @property int $num 入库数量
 * @property string $unit 单位
 * @property string $total_price 总价
 * @property int $actual_num 实际数量
 * @property string $remarks 备注
 * @property string $area_id_name 区域名称
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class StockInDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_in_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['in_id', 'commodity_id', 'num', 'actual_num', 'is_delete', 'create_time','type_id','type_first_tier_id'], 'integer'],
            [['commodity_id', 'commodity_name', 'num'], 'required'],
            [['price', 'total_price'], 'number'],
            [['in_no', 'commodity_name', 'unit', 'area_id_name'], 'string', 'max' => 50],
            [['remarks','pic'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'in_id' => '入库ID',
            'in_no' => '入库单号',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'pic' => '商品图片',
            'price' => '入库价格',
            'num' => '入库数量',
            'unit' => '单位',
            'total_price' => '总价',
            'actual_num' => '实际数量',
            'remarks' => '备注',
            'area_id_name' => '区域名称',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
