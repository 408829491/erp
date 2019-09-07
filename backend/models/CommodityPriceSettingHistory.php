<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%commodity_price_setting_history}}".
 *
 * @property int $id ID
 * @property string $intelligent_no 定价编号
 * @property int $commodity_num 商品数量
 * @property int $sync_type 同步类型
 * @property string $sync_type_desc 同步描述
 * @property string $create_user 制单人
 * @property int $create_time 创建时间
 */
class CommodityPriceSettingHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%commodity_price_setting_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commodity_num', 'sync_type', 'create_time'], 'integer'],
            [['intelligent_no', 'sync_type_desc', 'create_user'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'intelligent_no' => '定价编号',
            'commodity_num' => '商品数量',
            'sync_type' => '同步类型',
            'sync_type_desc' => '同步描述',
            'create_user' => '制单人',
            'create_time' => '创建时间',
        ];
    }
}
