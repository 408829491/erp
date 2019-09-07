<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_commodity_profile_detail".
 *
 * @property string $id
 * @property string $commodity_profile_id 商品单位id
 * @property string $commodity_id 商品id
 * @property int $type_id 类型
 * @property double $price 价格
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CommodityProfileDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity_profile_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_profile_id', 'commodity_id', 'type_id'], 'integer'],
            [['price'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commodity_profile_id' => 'Commodity Profile ID',
            'commodity_id' => 'Commodity ID',
            'type_id' => 'Type ID',
            'price' => 'Price',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
