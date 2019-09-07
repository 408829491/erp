<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_delivery_line".
 *
 * @property int $id ID
 * @property string $name 线路名称
 * @property int $driver_id 司机ID
 * @property string $driver_name 司机姓名
 * @property string $driver_tel 司机电话
 * @property int $create_time 创建时间
 */
class DeliveryLine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_delivery_line';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['driver_id', 'create_time','is_delete'], 'integer'],
            [['name', 'driver_name','driver_tel','gps_imei'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '线路名称',
            'driver_id' => '司机ID',
            'driver_name' => '司机姓名',
            'driver_tel' => '司机电话',
            'create_time' => '创建时间',
        ];
    }
}
