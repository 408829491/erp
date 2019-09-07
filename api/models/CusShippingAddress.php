<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cus_shipping_address".
 *
 * @property string $id
 * @property string $member_id 用户id
 * @property double $lng 位置经度
 * @property double $lat 位置纬度
 * @property string $relation_people 联系人姓名
 * @property string $relation_phone 联系人电话
 * @property string $address_name 地址名称
 * @property string $district 省市区
 * @property string $address 范围地址
 * @property string $address_detail 详细地址
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusShippingAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_shipping_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id'], 'integer'],
            [['lng', 'lat'], 'required'],
            [['lng', 'lat'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['relation_people', 'relation_phone', 'address_name', 'district'], 'string', 'max' => 255],
            [['address', 'address_detail'], 'string', 'max' => 999],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'lng' => 'Lng',
            'lat' => 'Lat',
            'relation_people' => 'Relation People',
            'relation_phone' => 'Relation Phone',
            'address_name' => 'Address Name',
            'district' => 'District',
            'address' => 'Address',
            'address_detail' => 'Address Detail',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
