<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_finance_account_settle_detail".
 *
 * @property int $id ID
 * @property string $refer_no 业务单号
 * @property int $bill_type 单据类型
 * @property string $bill_type_text 单据类型描述
 * @property string $should_price 应付金额
 * @property string $pay_price 已付金额
 * @property string $actual_price 实付金额
 * @property string $reduction_price 抹零金额
 * @property string $remark 描述
 * @property int $create_time 创建时间
 */
class FinanceAccountSettlePurchaseDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_finance_account_settle_purchase_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refer_no','settle_id'], 'required'],
            [['bill_type', 'create_time'], 'integer'],
            [['should_price', 'pay_price', 'actual_price', 'reduction_price'], 'number'],
            [['refer_no'], 'string', 'max' => 50],
            [['bill_type_text'], 'string', 'max' => 100],
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
            'settle_id' => '结算单id',
            'refer_no' => '业务单号',
            'bill_type' => '单据类型',
            'bill_type_text' => '单据类型描述',
            'should_price' => '应付金额',
            'pay_price' => '已付金额',
            'actual_price' => '实付金额',
            'reduction_price' => '抹零金额',
            'remark' => '描述',
            'create_time' => '创建时间',
        ];
    }
}
