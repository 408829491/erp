<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_commodity_favor".
 *
 * @property int $id ID
 * @property int $commodity_id 商品ID
 * @property int $user_id 用户ID
 * @property int $create_time 创建时间
 */
class CommodityFavor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity_favor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_id', 'user_id', 'create_time', 'status'], 'integer'],
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
            'user_id' => '用户ID',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }
}
