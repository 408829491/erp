<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_store".
 *
 * @property string $id
 * @property string $name 门店名称
 * @property string $img 门店大图
 * @property string $address 店铺地址
 * @property double $lng 位置经度
 * @property double $lat 位置纬度
 * @property int $limit_delivery_meter 限制配送的距离（单位米）
 * @property string $relation_face_url 联系人头像
 * @property string $relation_people 联系人
 * @property string $relation_phone 联系电话
 * @property string $limit_send_price 起送费
 * @property string $delivery_cost 配送费
 * @property int $type 店铺类型(0活动门店,1农村福祉店)
 * @property string $info 备注
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusStore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lng', 'lat'], 'required'],
            [['lng', 'lat', 'limit_send_price', 'delivery_cost'], 'number'],
            [['limit_delivery_meter', 'type','is_sync'], 'integer'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name', 'address', 'relation_people', 'relation_phone','app_id','app_key'], 'string', 'max' => 255],
            [['img', 'relation_face_url', 'info'], 'string', 'max' => 999],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'img' => 'Img',
            'address' => 'Address',
            'lng' => 'Lng',
            'lat' => 'Lat',
            'limit_delivery_meter' => 'Limit Delivery Meter',
            'relation_face_url' => 'Relation Face Url',
            'relation_people' => 'Relation People',
            'relation_phone' => 'Relation Phone',
            'limit_send_price' => 'Limit Send Price',
            'delivery_cost' => 'Delivery Cost',
            'type' => 'Type',
            'info' => 'Info',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
