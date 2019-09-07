<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_discount_coupon_get_record".
 *
 * @property string $id
 * @property int $cus_member_id 领取用户的id
 * @property int $discount_coupon_id 优惠券id
 * @property string $store_id 商店id(-1全平台通用)
 * @property string $name 名称
 * @property string $start_date 开始日期
 * @property string $end_date 结束日期
 * @property string $info 描述
 * @property double $condition 满
 * @property double $distance 减
 * @property string $pic 图片
 * @property int $is_use 是否使用
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusDiscountCouponGetRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_discount_coupon_get_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cus_member_id', 'discount_coupon_id', 'store_id', 'is_use'], 'integer'],
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
            'cus_member_id' => 'Cus Member ID',
            'discount_coupon_id' => 'Discount Coupon ID',
            'store_id' => 'Store ID',
            'name' => 'Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'info' => 'Info',
            'condition' => 'Condition',
            'distance' => 'Distance',
            'pic' => 'Pic',
            'is_use' => 'Is Use',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
