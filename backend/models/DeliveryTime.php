<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_delivery_time".
 *
 * @property int $id ID
 * @property string $time_range 时间段
 * @property int $create_time 创建时间
 */
class DeliveryTime extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_delivery_time';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['time_range'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_range' => '时间段',
            'create_time' => '创建时间',
        ];
    }
}
