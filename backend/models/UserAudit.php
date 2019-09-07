<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_user_audit".
 *
 * @property string $id
 * @property string $audit_no 对账单号
 * @property int $source_id 来源id
 * @property string $source_no 来源单号
 * @property int $type 类型（1销售订单）
 * @property double $total_price 总金额
 * @property double $audit_price 对账金额
 * @property double $received_price 已收金额
 * @property int $pay_way 付款方式（1货到付款）
 * @property int $user_id 客户id
 * @property string $user_name 客户名称
 * @property string $user_phone 客户手机
 * @property int $operation_id 创建人id
 * @property string $operation_name 创建人名称
 * @property bool $is_audit 是否对账
 * @property int $audit_man_id 对账人id
 * @property string $audit_man_name 对账人名称
 * @property string $audit_time 对账时间
 * @property bool $is_settlement 是否结算
 * @property string $settlement_time 结算时间
 * @property int $line_id 线路Id
 * @property string $line_name 线路名称
 * @property int $line_sequence 线路排序
 * @property int $driver_id 司机ID
 * @property string $driver_name 司机姓名
 * @property string $delivery_date 发货日期
 * @property string $delivery_time_detail 发货时间段
 * @property string $info 备注
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class UserAudit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_user_audit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_id', 'type', 'pay_way', 'user_id', 'operation_id', 'audit_man_id', 'line_id', 'line_sequence', 'driver_id'], 'integer'],
            [['total_price', 'audit_price', 'received_price'], 'number'],
            [['is_audit', 'is_settlement'], 'boolean'],
            [['audit_time', 'settlement_time', 'delivery_date', 'create_datetime', 'modify_datetime'], 'safe'],
            [['audit_no', 'user_name', 'user_phone', 'operation_name', 'audit_man_name'], 'string', 'max' => 255],
            [['source_no', 'line_name'], 'string', 'max' => 100],
            [['driver_name', 'delivery_time_detail'], 'string', 'max' => 50],
            [['info'], 'string', 'max' => 999],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'audit_no' => 'Audit No',
            'source_id' => 'Source ID',
            'source_no' => 'Source No',
            'type' => 'Type',
            'total_price' => 'Total Price',
            'audit_price' => 'Audit Price',
            'received_price' => 'Received Price',
            'pay_way' => 'Pay Way',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_phone' => 'User Phone',
            'operation_id' => 'Operation ID',
            'operation_name' => 'Operation Name',
            'is_audit' => 'Is Audit',
            'audit_man_id' => 'Audit Man ID',
            'audit_man_name' => 'Audit Man Name',
            'audit_time' => 'Audit Time',
            'is_settlement' => 'Is Settlement',
            'settlement_time' => 'Settlement Time',
            'line_id' => 'Line ID',
            'line_name' => 'Line Name',
            'line_sequence' => 'Line Sequence',
            'driver_id' => 'Driver ID',
            'driver_name' => 'Driver Name',
            'delivery_date' => 'Delivery Date',
            'delivery_time_detail' => 'Delivery Time Detail',
            'info' => 'Info',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
