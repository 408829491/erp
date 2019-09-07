<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_commodity_profile".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property string $name 单位
 * @property string $price 价格
 * @property string $desc 描述
 * @property int $is_basics_unit 是否基础单位
 * @property int $base_self_ratio 基础单位与本单位的置换量
 * @property int $is_sell 是否可卖
 * @property int $source_from 来源
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusCommodityProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_commodity_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'name'], 'required'],
            [['commodity_id', 'is_basics_unit', 'base_self_ratio', 'is_sell', 'source_from'], 'integer'],
            [['price'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 999],
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
            'name' => '单位',
            'price' => '价格',
            'desc' => '描述',
            'is_basics_unit' => '是否基础单位',
            'base_self_ratio' => '基础单位与本单位的置换量',
            'is_sell' => '是否可卖',
            'source_from' => '来源',
            'create_datetime' => '创建日期时间',
            'modify_datetime' => '修改日期时间',
        ];
    }
}
