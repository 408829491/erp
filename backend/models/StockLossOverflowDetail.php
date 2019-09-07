<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_loss_overflow_detail}}".
 *
 * @property int $id ID
 * @property int $loss_over_id 单据ID
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property string $type_name 主分类
 * @property string $category2 子分类
 * @property string $unit 单位
 * @property int $num 数量
 * @property int $actual_num 实际数量
 * @property string $price 成本价
 * @property string $total_price 总价
 * @property int $sell_stock 系统库存
 * @property string $remark 备注
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class StockLossOverflowDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_loss_overflow_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loss_over_id', 'commodity_id', 'num', 'actual_num', 'is_delete', 'create_time'], 'integer'],
            [['price', 'total_price','sell_stock'], 'number'],
            [['commodity_name', 'type_name', 'category2'], 'string', 'max' => 50],
            [['unit'], 'string', 'max' => 30],
            [['remark','pic'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loss_over_id' => '单据ID',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'pic' => '商品图片',
            'type_name' => '主分类',
            'category2' => '子分类',
            'unit' => '单位',
            'num' => '数量',
            'actual_num' => '实际数量',
            'price' => '成本价',
            'total_price' => '总价',
            'sell_stock' => '系统库存',
            'remark' => '备注',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
