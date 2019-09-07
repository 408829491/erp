<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_commodity_demand".
 *
 * @property int $id ID
 * @property string $commodity_name 商品名称
 * @property int $t_id 分类ID
 * @property int $p_id 父类ID
 * @property string $brand 品牌
 * @property string $price 价格
 * @property string $describe 描述
 * @property int $user_id 用户ID
 * @property string $user_name 用户名
 * @property int $status 状态
 * @property int $create_time 创建时间
 * @property int $checked_time 提议通过时间
 * @property int $online_time 上线时间
 */
class CommodityDemand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity_demand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['t_id', 'p_id', 'user_id', 'status', 'create_time', 'checked_time', 'online_time'], 'integer'],
            [['price'], 'number'],
            [['commodity_name', 'describe'], 'string', 'max' => 255],
            [['brand'], 'string', 'max' => 60],
            [['user_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commodity_name' => 'Commodity Name',
            't_id' => 'T ID',
            'p_id' => 'P ID',
            'brand' => 'Brand',
            'price' => 'Price',
            'describe' => 'Describe',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'checked_time' => 'Checked Time',
            'online_time' => 'Online Time',
        ];
    }
}
