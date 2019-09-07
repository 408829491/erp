<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_user_audit_detail".
 *
 * @property string $id
 * @property int $user_audit_id 用户对账id
 * @property string $user_audit_no 用户对账单号
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property int $type_first_tier_id 一级分类Id
 * @property int $type_id 分类Id
 * @property string $parent_type_name 一级分类名称
 * @property string $type_name 商品分类
 * @property string $pic 商品图片
 * @property string $unit 单位
 * @property string $notice 商品描述
 * @property string $in_price 成本价
 * @property int $channel_type 采购类型
 * @property string $price 单价
 * @property string $actual_num 实际分拣数量
 * @property string $total_price 发货金额小计
 * @property int $is_basics_unit 是否基础单位
 * @property double $base_self_ratio 换算比率
 * @property string $base_unit 基础单位
 * @property string $diff_total_price 差异总价
 * @property string $diff_price 差异单价
 * @property string $diff_num 差异数量
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class UserAuditDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_user_audit_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_audit_id', 'commodity_id', 'type_first_tier_id', 'type_id', 'channel_type', 'is_basics_unit'], 'integer'],
            [['unit'], 'required'],
            [['notice'], 'string'],
            [['in_price', 'price', 'actual_num', 'total_price', 'base_self_ratio', 'diff_total_price', 'diff_price', 'diff_num'], 'number'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['user_audit_no', 'pic'], 'string', 'max' => 255],
            [['commodity_name'], 'string', 'max' => 100],
            [['parent_type_name', 'type_name'], 'string', 'max' => 50],
            [['unit'], 'string', 'max' => 20],
            [['base_unit'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_audit_id' => 'User Audit ID',
            'user_audit_no' => 'User Audit No',
            'commodity_id' => 'Commodity ID',
            'commodity_name' => 'Commodity Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'parent_type_name' => 'Parent Type Name',
            'type_name' => 'Type Name',
            'pic' => 'Pic',
            'unit' => 'Unit',
            'notice' => 'Notice',
            'in_price' => 'In Price',
            'channel_type' => 'Channel Type',
            'price' => 'Price',
            'actual_num' => 'Actual Num',
            'total_price' => 'Total Price',
            'is_basics_unit' => 'Is Basics Unit',
            'base_self_ratio' => 'Base Self Ratio',
            'base_unit' => 'Base Unit',
            'diff_total_price' => 'Diff Total Price',
            'diff_price' => 'Diff Price',
            'diff_num' => 'Diff Num',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
