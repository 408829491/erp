<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_commodity_profile".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property string $name 单位
 * @property string $price 价格
 * @property string $desc 描述
 * @property int $is_basics_unit 是否基础单位
 * @property int $base_self_ratio 基础单位与本单位的置换比例
 * @property int $is_sell 是否可卖
 * @property int $is_delete 是否删除
 * @property int $source_from 来源
 */
class CommodityProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'name'], 'required'],
            [['commodity_id', 'is_basics_unit', 'is_sell', 'is_delete', 'source_from'], 'integer'],
            [['price', 'base_self_ratio'], 'number'],
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
            'commodity_id' => 'Commodity ID',
            'name' => 'Name',
            'price' => 'Price',
            'desc' => 'Desc',
            'is_basics_unit' => 'Is Basics Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'is_sell' => 'Is Sell',
            'is_delete' => 'Is Delete',
            'source_from' => 'Source From',
        ];
    }
}
