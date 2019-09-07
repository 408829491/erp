<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_purchase_detail".
 *
 * @property int $id ID
 * @property int $category_id 大类ID
 * @property int $category_id2 子类ID
 * @property string $type_name 类别
 * @property int $commodity_id 商品ID
 * @property string $commodity_code 商品编码
 * @property string $last_in_price 最后采购价
 * @property string $inquiry_price 最近询价
 * @property string $logo 商品图片
 * @property string $name 商品名称
 * @property int $num 已收数量
 * @property string $price 进货均价
 * @property int $purchase_num 待采购数量
 * @property string $purchase_price
 * @property string $purchase_id
 * @property string $purchase_total_price
 * @property int $return_num 退货数量
 * @property int $status 状态
 * @property string $total_price 收货总价
 * @property string $unit 商品单位
 * @property int $unreceive 未收货数量
 * @property string $pinyin 商品拼音码
 * @property int $create_time 创建时间
 */
class PurchaseDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_purchase_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_first_tier_id', 'type_id', 'commodity_id', 'num', 'purchase_num', 'return_num', 'status', 'unreceive', 'create_time','purchase_id'], 'integer'],
            [['last_in_price', 'inquiry_price', 'price', 'purchase_price', 'purchase_total_price', 'total_price'], 'number'],
            [['type_name', 'name'], 'string', 'max' => 100],
            [['commodity_name','remark'], 'string', 'max' => 255],
            [['pic', 'pinyin'], 'string', 'max' => 300],
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
            'category_id' => '大类ID',
            'category_id2' => '子类ID',
            'type_name' => '类别',
            'commodity_id' => '商品ID',
            'commodity_name' => '商品名称',
            'last_in_price' => '最后采购价',
            'inquiry_price' => '最近询价',
            'pic' => '商品图片',
            'name' => '商品名称',
            'num' => '已收数量',
            'price' => '进货均价',
            'purchase_num' => '待采购数量',
            'purchase_id' => '采购单ID',
            'purchase_price' => 'Purchase Price',
            'purchase_total_price' => 'Purchase Total Price',
            'return_num' => '退货数量',
            'status' => '状态',
            'total_price' => '收货总价',
            'unit' => '商品单位',
            'unreceive' => '未收货数量',
            'pinyin' => '商品拼音码',
            'remark' => '备注',
            'create_time' => '创建时间',
        ];
    }
}
