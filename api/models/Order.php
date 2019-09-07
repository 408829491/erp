<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_order".
 *
 * @property int $id 自增ID
 * @property string $order_no 订单号
 * @property string $price 订单价格
 * @property string $pay_price 已支付价格
 * @property int $pay_way 支付方式，0货到付款，1在线支付
 * @property string $pay_way_text 支付方式描述
 * @property int $pay_time 支付时间
 * @property string $pay_record_no 支付流水号
 * @property int $pay_record_no_expire 支否支付超时
 * @property int $status 状态码
 * @property string $status_text 状码码描述
 * @property string $is_pay 是否已支付
 * @property string $is_pay_text 状态提示
 * @property string $line_name 线路名称
 * @property int $line_sequence 线路排序
 * @property int $driver_id 司机ID
 * @property string $driver_name 司机姓名
 * @property string $delivery_date 发货日期
 * @property string $delivery_time_detail 发货时间段
 * @property int $delivery_time_id 发货时间段ID
 * @property string $address_detail 收货详细地址
 * @property string $receive_name 收货人姓名
 * @property string $receive_tel 收货人电话
 * @property string $freight_price 运费
 * @property string $reduction_price 优惠价格
 * @property string $remark 备注
 * @property int $user_id 下单客户ID
 * @property string $user_name 下单客户
 * @property int $close_time 订单关闭时间
 * @property int $close_type 订单关闭类型
 * @property int $audit_time 审核时间
 * @property int $audit_user_id 审核人ID
 * @property string $coupon_code 优惠券ID
 * @property int $source 订单来源ID
 * @property string $source_txt 订单来源描述
 * @property int $return_num 退货数量
 * @property string $return_price 退货金额
 * @property int $create_time 创建时间
 */
class Order extends \yii\db\ActiveRecord
{
    public $commodity_list;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_no', 'delivery_date', 'delivery_time_detail', 'receive_name', 'receive_tel', 'user_name', 'source', 'create_time'], 'required'],
            [['price', 'pay_price', 'freight_price', 'reduction_price', 'return_price'], 'number'],
            [['pay_way', 'pay_time', 'pay_record_no_expire', 'status', 'line_sequence', 'driver_id', 'delivery_time_id', 'user_id', 'close_time', 'close_type', 'audit_time', 'audit_user_id', 'source', 'return_num', 'create_time','exception_status','achieve_date','c_type','salesman_id'], 'integer'],
            [['is_pay','nick_name'], 'string'],
            [['order_no', 'status_text', 'line_name','address_name'], 'string', 'max' => 100],
            [['pay_way_text', 'pay_record_no', 'is_pay_text', 'driver_name', 'delivery_date', 'delivery_time_detail', 'receive_name', 'receive_tel', 'user_name', 'coupon_code', 'source_txt','address_lat','address_lng','salesman_name'], 'string', 'max' => 50],
            [['address_detail', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'order_no' => '订单号',
            'price' => '订单价格',
            'pay_price' => '已支付价格',
            'pay_way' => '支付方式，0货到付款，1在线支付',
            'pay_way_text' => '支付方式描述',
            'pay_time' => '支付时间',
            'pay_record_no' => '支付流水号',
            'pay_record_no_expire' => '支否支付超时',
            'status' => '状态码',
            'status_text' => '状码码描述',
            'is_pay' => '是否已支付',
            'is_pay_text' => '状态提示',
            'line_name' => '线路名称',
            'line_sequence' => '线路排序',
            'driver_id' => '司机ID',
            'driver_name' => '司机姓名',
            'delivery_date' => '发货日期',
            'delivery_time_detail' => '发货时间段',
            'delivery_time_id' => '发货时间段ID',
            'address_detail' => '收货详细地址',
            'receive_name' => '收货人姓名',
            'receive_tel' => '收货人电话',
            'freight_price' => '运费',
            'reduction_price' => '优惠价格',
            'remark' => '备注',
            'user_id' => '下单客户ID',
            'user_name' => '下单客户',
            'nick_name' => '客户名称',
            'close_time' => '订单关闭时间',
            'close_type' => '订单关闭类型',
            'audit_time' => '审核时间',
            'audit_user_id' => '审核人ID',
            'coupon_code' => '优惠券ID',
            'source' => '订单来源ID',
            'source_txt' => '订单来源描述',
            'return_num' => '退货数量',
            'return_price' => '退货金额',
            'create_time' => '创建时间',
        ];
    }

    /**
     * 新增属性
     * @return array
     */
    public function attributes() {
        return array_merge(parent::attributes(), ['detail', 'create_time_ymd', 'total_num']);
    }

    /**
     * 关联订单商品
     * @return mixed
     */
    public function getDetails()
    {
        return $this->hasMany(OrderDetail::className(), ['order_id' => 'id']);
    }
}
