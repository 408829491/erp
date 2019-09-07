<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%stock_loss_overflow}}".
 *
 * @property int $id ID
 * @property string $no 单号
 * @property int $type 类型
 * @property string $type_name 类型描述
 * @property int $store_id 仓库ID
 * @property string $store_name 仓库名称
 * @property int $status 状态
 * @property string $status_name 状态描述
 * @property int $num 数量
 * @property string $total_price 总价
 * @property string $remark 备注
 * @property int $check_time 审核时间
 * @property string $check_user 审核用户
 * @property int $check_user_id 审核用户ID
 * @property string $create_user 制单人
 * @property int $create_time 创建时间
 */
class StockLossOverflow extends \yii\db\ActiveRecord
{
    public $commodity_list = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_loss_overflow}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'store_id', 'status', 'num', 'check_time', 'check_user_id', 'create_time'], 'integer'],
            [['total_price'], 'number'],
            [['no', 'type_name', 'store_name', 'create_user'], 'string', 'max' => 50],
            [['status_name'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
            [['check_user'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'no' => '单号',
            'type' => '类型',
            'type_name' => '类型描述',
            'store_id' => '仓库ID',
            'store_name' => '仓库名称',
            'status' => '状态',
            'status_name' => '状态描述',
            'num' => '数量',
            'total_price' => '总价',
            'remark' => '备注',
            'check_time' => '审核时间',
            'check_user' => '审核用户',
            'check_user_id' => '审核用户ID',
            'create_user' => '制单人',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 关联商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(StockLossOverflowDetail::className(), ['loss_over_id' => 'id']);
    }
}
