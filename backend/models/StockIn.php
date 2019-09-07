<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_in}}".
 *
 * @property int $id ID
 * @property string $in_no 入库单号
 * @property int $in_num 数量
 * @property int $status 状态ID
 * @property string $status_name 状态描述
 * @property int $type 入库类型
 * @property string $type_name 入库类型描述
 * @property int $in_time 入库时间
 * @property int $purchase_time 采购时间
 * @property int $op_id 操作员ID
 * @property string $operator 操作员用户名
 * @property int $provider_id 供应商ID
 * @property string $about_no 关联单号
 * @property int $store_id 仓库ID
 * @property string $store_id_name 仓库名称
 * @property string $total_price 总价
 * @property string $remark 描述
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class StockIn extends \yii\db\ActiveRecord
{
    public $commodity_list = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_in}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['in_no'], 'required'],
            [['in_num', 'status', 'type', 'in_time', 'purchase_time','purchase_type', 'op_id', 'provider_id', 'provider_name', 'store_id', 'is_delete', 'create_time'], 'integer'],
            [['total_price'], 'number'],
            [['in_no', 'status_name', 'type_name', 'operator', 'about_no', 'store_id_name'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'in_no' => '入库单号',
            'in_num' => '数量',
            'status' => '状态ID',
            'status_name' => '状态描述',
            'in_type' => '入库类型',
            'in_type_name' => '入库类型描述',
            'in_time' => '入库时间',
            'purchase_time' => '采购时间',
            'op_id' => '操作员ID',
            'operator' => '操作员用户名',
            'provider_id' => '供应商ID',
            'about_no' => '关联单号',
            'store_id' => '仓库ID',
            'store_id_name' => '仓库名称',
            'total_price' => '总价',
            'remark' => '描述',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 关联商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(StockInDetail::className(), ['in_id' => 'id']);
    }
}
