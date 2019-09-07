<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_discount_coupon".
 *
 * @property string $id
 * @property string $store_id 商店id(-1全平台通用)
 * @property string $name 名称
 * @property string $start_date 开始日期
 * @property string $end_date 结束日期
 * @property string $info 描述
 * @property double $condition 满
 * @property double $distance 减
 * @property string $pic 图片
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusDiscountCoupon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_discount_coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id'], 'integer'],
            [['start_date', 'end_date', 'create_datetime', 'modify_datetime'], 'safe'],
            [['condition', 'distance'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['info', 'pic'], 'string', 'max' => 999],
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
            'name' => 'Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'info' => 'Info',
            'condition' => 'Condition',
            'distance' => 'Distance',
            'pic' => 'Pic',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
