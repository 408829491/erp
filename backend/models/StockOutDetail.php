<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_stock_out_detail".
 *
 * @property int $id ID
 * @property int $out_id 出库ID
 * @property string $out_no 出库单号
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property string $pic 商品图片
 * @property string $price 出库价格
 * @property string $num 出库数量
 * @property string $unit 单位
 * @property bool $is_basics_unit 是否基础单位
 * @property double $base_self_ratio 换算比例
 * @property string $total_price 总价
 * @property string $actual_num 实际数量
 * @property string $remarks 备注
 * @property string $area_id_name 区域名称
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class StockOutDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_stock_out_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['out_id', 'commodity_id', 'is_delete', 'create_time'], 'integer'],
            [['commodity_id', 'commodity_name', 'num'], 'required'],
            [['price', 'num', 'base_self_ratio', 'total_price', 'actual_num'], 'number'],
            [['is_basics_unit'], 'boolean'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['out_no', 'commodity_name', 'unit', 'area_id_name'], 'string', 'max' => 50],
            [['pic', 'remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'out_id' => 'Out ID',
            'out_no' => 'Out No',
            'commodity_id' => 'Commodity ID',
            'commodity_name' => 'Commodity Name',
            'pic' => 'Pic',
            'price' => 'Price',
            'num' => 'Num',
            'unit' => 'Unit',
            'is_basics_unit' => 'Is Basics Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'total_price' => 'Total Price',
            'actual_num' => 'Actual Num',
            'remarks' => 'Remarks',
            'area_id_name' => 'Area Id Name',
            'is_delete' => 'Is Delete',
            'create_time' => 'Create Time',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }

    /**
     * 关联商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(StockOutDetail::className(), ['out_id' => 'id']);
    }
}
