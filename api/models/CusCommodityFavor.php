<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_commodity_favor".
 *
 * @property int $id ID
 * @property int $store_id 店铺id
 * @property int $commodity_id 商品ID
 * @property int $status 收藏状态1收藏0未收藏
 * @property int $user_id 用户ID
 * @property int $create_time 创建时间
 */
class CusCommodityFavor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_commodity_favor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'commodity_id', 'status', 'user_id', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'commodity_id' => 'Commodity ID',
            'status' => 'Status',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
        ];
    }
}
