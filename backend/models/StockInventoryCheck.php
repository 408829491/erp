<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_inventory_check}}".
 *
 * @property int $id ID
 * @property string $check_no 盘点单号
 * @property int $store_id 仓库ID
 * @property string $store_name 仓库名称
 * @property int $op_id 操作员ID
 * @property string $operator 操作员
 * @property int $status 状态
 * @property string $status_name 状态描述
 * @property string $overflow_price 盘溢金额
 * @property string $loss_price 盘损金额
 * @property int $type 类型
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class StockInventoryCheck extends \yii\db\ActiveRecord
{
    public $commodity_list = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_inventory_check}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['check_no'], 'required'],
            [['store_id', 'op_id', 'status', 'type', 'is_delete', 'create_time'], 'integer'],
            [['overflow_price', 'loss_price'], 'number'],
            [['check_no', 'store_name', 'operator'], 'string', 'max' => 50],
            [['status_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'check_no' => '盘点单号',
            'store_id' => '仓库ID',
            'store_name' => '仓库名称',
            'op_id' => '操作员ID',
            'operator' => '操作员',
            'status' => '状态',
            'status_name' => '状态描述',
            'overflow_price' => '盘溢金额',
            'loss_price' => '盘损金额',
            'type' => '类型',
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
        return $this->hasMany(StockInventoryCheckDetail::className(), ['check_id' => 'id']);
    }
}
