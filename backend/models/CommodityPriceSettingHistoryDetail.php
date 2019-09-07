<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%commodity_price_setting_history_detail}}".
 *
 * @property int $id ID
 * @property int $h_id 定价ID
 * @property string $commodity_name 商品名称
 * @property int $commodity_id 商品id
 * @property int $c_type 客户类型
 * @property string $unit 单位
 * @property string $in_price 最近进价
 * @property string $pre_price 之前价格
 * @property string $after_price 最新价格
 * @property int $create_time 创建时间
 */
class CommodityPriceSettingHistoryDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%commodity_price_setting_history_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'h_id', 'commodity_id', 'c_type', 'create_time'], 'integer'],
            [['in_price', 'pre_price', 'after_price'], 'number'],
            [['commodity_name', 'unit'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'h_id' => '定价ID',
            'commodity_name' => '商品名称',
            'commodity_id' => '商品id',
            'c_type' => '客户类型',
            'unit' => '单位',
            'in_price' => '最近进价',
            'pre_price' => '之前价格',
            'after_price' => '最新价格',
            'create_time' => '创建时间',
        ];
    }
}
