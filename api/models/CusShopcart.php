<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_shopcart".
 *
 * @property string $id
 * @property string $store_id 店铺id
 * @property int $cus_member_id 用户id
 * @property int $type 类型(0普通，1促销)
 * @property int $source_id 来源id
 * @property string $name 名称
 * @property string $pic 图片
 * @property string $price 价格
 * @property string $unit 单位
 * @property int $is_basics_unit 是否基础单位
 * @property int $base_self_ratio 基础单位与本单位的置换量
 * @property string $basic_unit 基础单位
 * @property int $num 数量
 * @property int $is_checked 是否选中
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusShopcart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_shopcart';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'cus_member_id', 'type', 'source_id', 'is_basics_unit', 'base_self_ratio', 'num', 'is_checked'], 'integer'],
            [['pic'], 'string'],
            [['price'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name', 'unit', 'basic_unit'], 'string', 'max' => 255],
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
            'cus_member_id' => 'Cus Member ID',
            'type' => 'Type',
            'source_id' => 'Source ID',
            'name' => 'Name',
            'pic' => 'Pic',
            'price' => 'Price',
            'unit' => 'Unit',
            'is_basics_unit' => 'Is Basics Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'basic_unit' => 'Basic Unit',
            'num' => 'Num',
            'is_checked' => 'Is Checked',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
