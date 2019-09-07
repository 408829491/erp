<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_seckill".
 *
 * @property int $id
 * @property string $store_id 门店id
 * @property string $name 活动名称
 * @property string $seckill_commoditys_name 抢购商品名称
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property int $buy_num 下单数量
 * @property int $is_close 是否主动关闭
 * @property int $is_limit_buy_num 是否限制购买数量
 * @property int $closer_id 关闭人ID
 * @property string $close_name 关闭人姓名
 * @property string $close_time 关闭时间
 * @property string $create_time 创建时间
 * @property string $modify_time 更新时间
 */
class CusSeckill extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_seckill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'buy_num', 'is_close', 'is_limit_buy_num', 'closer_id'], 'integer'],
            [['seckill_commoditys_name'], 'string'],
            [['start_time', 'end_time', 'close_time', 'create_time', 'modify_time'], 'safe'],
            [['name', 'close_name'], 'string', 'max' => 255],
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
            'seckill_commoditys_name' => 'Seckill Commoditys Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'buy_num' => 'Buy Num',
            'is_close' => 'Is Close',
            'is_limit_buy_num' => 'Is Limit Buy Num',
            'closer_id' => 'Closer ID',
            'close_name' => 'Close Name',
            'close_time' => 'Close Time',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
        ];
    }
}
