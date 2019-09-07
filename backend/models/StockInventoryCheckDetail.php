<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_inventory_check_detail}}".
 *
 * @property int $id ID
 * @property int $check_id 盘点单ID
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property string $type_name 主分类
 * @property string $category2 子分类
 * @property string $price 价格
 * @property string $unit 单位
 * @property string $area_name 区域
 * @property int $existing 系统存存
 * @property int $actual_existing 实际数量
 * @property string $remark 备注
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class StockInventoryCheckDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_inventory_check_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_id', 'commodity_id', 'num', 'is_delete', 'create_time'], 'integer'],
            [['price','sell_stock'], 'number'],
            [['commodity_name', 'type_name', 'category2', 'area_name'], 'string', 'max' => 50],
            [['unit'], 'string', 'max' => 20],
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
            'check_id' => '盘点单ID',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'type_name' => '主分类',
            'category2' => '子分类',
            'price' => '价格',
            'unit' => '单位',
            'area_name' => '区域',
            'sell_stock' => '系统存存',
            'num' => '实际数量',
            'remark' => '备注',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
