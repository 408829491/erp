<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_finance_account_settle".
 *
 * @property int $id ID
 * @property string $settle_no 结算单号
 * @property string $user_name 用户名
 * @property string $price 价格
 * @property string $refer_no 业务单号
 * @property string $create_user 制单人
 * @property string $pay_user 交款人
 * @property int $status 状态
 * @property string $status_text 状态描述
 * @property int $pay_way 付款方式
 * @property string $pay_way_text 付款描述
 * @property string $actual_price 实付金额
 * @property string $pic 凭证
 * @property string $reduction_price 抹零金额
 * @property string $remark 备注
 * @property int $create_time 创建时间
 */
class CusFinanceAccountSettle extends \yii\db\ActiveRecord
{
    public $list = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_finance_account_settle';
    }

    /**l
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settle_no', 'create_time'], 'required'],
            [['price', 'actual_price', 'reduction_price'], 'number'],
            [['status', 'pay_way', 'create_time'], 'integer'],
            [['settle_no', 'user_name', 'refer_no', 'create_user', 'pay_user'], 'string', 'max' => 50],
            [['status_text', 'pay_way_text'], 'string', 'max' => 20],
            [['pic', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settle_no' => '结算单号',
            'user_name' => '用户名',
            'price' => '价格',
            'refer_no' => '业务单号',
            'create_user' => '制单人',
            'pay_user' => '交款人',
            'status' => '状态',
            'status_text' => '状态描述',
            'pay_way' => '付款方式',
            'pay_way_text' => '付款描述',
            'actual_price' => '实付金额',
            'pic' => '凭证',
            'reduction_price' => '抹零金额',
            'remark' => '备注',
            'create_time' => '创建时间',
        ];
    }
}
