<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_stock_out".
 *
 * @property int $id ID
 * @property string $out_no 出库单号
 * @property string $num 出库数量
 * @property string $total_price 总价
 * @property int $op_id 操作员ID
 * @property string $operator 操作员
 * @property int $status 状态
 * @property string $status_name 状态描述
 * @property int $store_id 仓库ID
 * @property string $store_id_name 仓库名称
 * @property int $type 类型
 * @property string $type_name 类型描述
 * @property int $about_id 关联ID
 * @property string $about_no 关联单号
 * @property int $is_delete 是否删除
 * @property string $out_time 出库时间
 * @property int $user_id 客户Id
 * @property string $user_name 客户名称
 * @property string $remark 备注
 * @property int $create_time 创建时间
 */
class StockOut extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_stock_out';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['out_no'], 'required'],
            [['num', 'total_price'], 'number'],
            [['op_id', 'status', 'store_id', 'type', 'about_id', 'is_delete', 'user_id', 'create_time'], 'integer'],
            [['out_time'], 'safe'],
            [['out_no', 'operator', 'status_name', 'store_id_name', 'type_name', 'about_no', 'user_name'], 'string', 'max' => 50],
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
            'out_no' => 'Out No',
            'num' => 'Num',
            'total_price' => 'Total Price',
            'op_id' => 'Op ID',
            'operator' => 'Operator',
            'status' => 'Status',
            'status_name' => 'Status Name',
            'store_id' => 'Store ID',
            'store_id_name' => 'Store Id Name',
            'type' => 'Type',
            'type_name' => 'Type Name',
            'about_id' => 'About ID',
            'about_no' => 'About No',
            'is_delete' => 'Is Delete',
            'out_time' => 'Out Time',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
        ];
    }

    public $commodity_list = [];

    /**
     * 关联商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(StockOutDetail::className(), ['out_id' => 'id']);
    }
}
