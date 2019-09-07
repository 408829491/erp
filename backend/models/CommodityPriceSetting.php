<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%commodity_price_setting}}".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property int $type 类型
 * @property int $c_type 类型
 * @property string $in_price 价格
 * @property string $recent_price 最近价格
 * @property string $unit 单位
 * @property string $add_value 值
 * @property string $option_user 操作员
 * @property int $create_time 创建时间
 */
class CommodityPriceSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%commodity_price_setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'type','c_type', 'create_time'], 'integer'],
            [['add_value', 'recent_price','in_price'], 'number'],
            [['option_user','unit'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commodity_id' => '商品ID',
            'type' => '类型',
            'c_type' => '客户类型',
            'add_value' => '价格',
            'recent_price' => '最近价格',
            'option_user' => '操作员',
            'create_time' => '创建时间',
        ];
    }
}
