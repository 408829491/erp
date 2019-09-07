<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_finance_account_settle".
 *
 * @property int $id ID
 * @property string $settle_no 结算单号
 * @property int $user_id 用户id
 * @property string $user_name 用户名
 * @property string $price 价格
 * @property string $refer_no 业务单号
 * @property int $create_user_id 制单人id
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
class FinanceAccountSettle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_finance_account_settle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settle_no', 'create_time'], 'required'],
            [['user_id', 'create_user_id', 'status', 'pay_way', 'create_time'], 'integer'],
            [['price', 'actual_price', 'reduction_price'], 'number'],
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
            'settle_no' => 'Settle No',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'price' => 'Price',
            'refer_no' => 'Refer No',
            'create_user_id' => 'Create User ID',
            'create_user' => 'Create User',
            'pay_user' => 'Pay User',
            'status' => 'Status',
            'status_text' => 'Status Text',
            'pay_way' => 'Pay Way',
            'pay_way_text' => 'Pay Way Text',
            'actual_price' => 'Actual Price',
            'pic' => 'Pic',
            'reduction_price' => 'Reduction Price',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
        ];
    }

    public $list = [];

    /**
     * 关联结算单详情
     * @return mixed
     */
    public function getDetail()
    {
        return $this->hasOne(FinanceAccountSettleDetail::className(), ['settle_id' => 'id']);
    }
}
