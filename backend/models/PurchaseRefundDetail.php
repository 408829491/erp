<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%purchase_refund_detail}}".
 *
 * @property int $id ID
 * @property int $refund_id 采购单ID
 * @property int $type_first_tier_id 大类ID
 * @property int $type_id 子类ID
 * @property string $type_name 类别
 * @property int $commodity_id 商品ID
 * @property string $commodity_name 商品名称
 * @property string $pic 商品图片
 * @property string $name 商品名称
 * @property int $num 退货数量
 * @property string $price 退货价格
 * @property int $status 状态
 * @property string $total_price 退货总价
 * @property string $unit 商品单位
 * @property string $pinyin 商品拼音码
 * @property string $remark 备注
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class PurchaseRefundDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%purchase_refund_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refund_id', 'type_first_tier_id', 'type_id', 'commodity_id', 'refund_num', 'status', 'is_delete', 'create_time'], 'integer'],
            [['refund_price', 'total_refund_price'], 'number'],
            [['type_name', 'name'], 'string', 'max' => 100],
            [['commodity_name', 'pinyin', 'remark'], 'string', 'max' => 255],
            [['pic'], 'string', 'max' => 300],
            [['unit'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refund_id' => '采购单ID',
            'type_first_tier_id' => '大类ID',
            'type_id' => '子类ID',
            'type_name' => '类别',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'pic' => '商品图片',
            'name' => '商品名称',
            'num' => '退货数量',
            'price' => '退货价格',
            'status' => '状态',
            'total_price' => '退货总价',
            'unit' => '商品单位',
            'pinyin' => '商品拼音码',
            'remark' => '备注',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
