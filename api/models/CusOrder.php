<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_order".
 *
 * @property int $id 自增ID
 * @property int $store_id 店铺id
 * @property string $store_name 店铺名称
 * @property string $store_img 店铺大图
 * @property string $order_no 订单号
 * @property string $price 订单价格
 * @property string $total_price 订单价格
 * @property string $pay_price 实际支付价格
 * @property int $pay_way 支付方式，0货到付款，1在线支付
 * @property string $pay_way_text 支付方式描述
 * @property int $pay_time 支付时间
 * @property string $pay_record_no 支付流水号
 * @property int $pay_record_no_expire 支否支付超时
 * @property int $status 状态码
 * @property string $status_text 状态码描述
 * @property string $is_pay 是否已支付
 * @property string $is_pay_text 状态提示
 * @property string $line_name 线路名称
 * @property int $line_sequence 线路排序
 * @property int $driver_id 司机ID
 * @property string $driver_name 司机姓名
 * @property string $delivery_date 发货日期
 * @property string $delivery_time_detail 发货时间段
 * @property int $delivery_time_id 发货时间段ID
 * @property int $is_send_to_home 是否配送到家（0到点自取，1配送到家）
 * @property double $address_lng 收货地址经度
 * @property double $address_lat 收货地址纬度
 * @property string $address_name 收货地址名称
 * @property string $address_district 收货地址省市区
 * @property string $address 收货地址范围
 * @property string $address_detail 收货详细地址
 * @property string $receive_name 收货人姓名
 * @property string $receive_tel 收货人电话
 * @property string $freight_price 运费
 * @property string $reduction_price 优惠价格
 * @property string $remark 备注
 * @property int $user_id 下单客户ID
 * @property string $user_name 客户用户名
 * @property string $nick_name 客户名称/姓名
 * @property int $close_time 订单关闭时间
 * @property int $close_type 订单关闭类型
 * @property int $coupon_id 优惠券ID
 * @property int $is_audit 是否对账
 * @property int $audit_time 审核时间
 * @property int $audit_user_id 审核人ID
 * @property int $source 订单来源ID
 * @property string $source_txt 订单来源描述
 * @property int $return_num 退货数量
 * @property string $return_price 退货金额
 * @property int $create_time 创建时间
 */
class CusOrder extends \yii\db\ActiveRecord
{
    public $commodity_list;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'pay_way', 'pay_time', 'pay_record_no_expire', 'status', 'line_sequence', 'driver_id', 'delivery_time_id', 'is_send_to_home', 'close_time', 'close_type', 'coupon_id', 'is_audit', 'audit_time', 'audit_user_id', 'source', 'return_num', 'create_time','exception_status','achieve_date'], 'integer'],
            [['order_no', 'delivery_date', 'delivery_time_detail', 'user_name', 'create_time'], 'required'],
            [['price', 'pay_price','total_profit', 'address_lng', 'address_lat', 'freight_price', 'reduction_price', 'return_price'], 'number'],
            [['is_pay'], 'string'],
            [['user_id'], 'number'],
            [['store_name', 'address_name', 'address_district', 'address_detail', 'remark'], 'string', 'max' => 255],
            [['store_img', 'address'], 'string', 'max' => 999],
            [['order_no', 'status_text', 'line_name'], 'string', 'max' => 100],
            [['pay_way_text', 'pay_record_no', 'is_pay_text', 'driver_name', 'delivery_date', 'delivery_time_detail', 'receive_name', 'receive_tel', 'user_name', 'nick_name', 'source_txt'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'store_name' => 'Store Name',
            'store_img' => 'Store Img',
            'order_no' => 'Order No',
            'price' => 'Price',
            'pay_price' => 'Pay Price',
            'pay_way' => 'Pay Way',
            'pay_way_text' => 'Pay Way Text',
            'pay_time' => 'Pay Time',
            'pay_record_no' => 'Pay Record No',
            'pay_record_no_expire' => 'Pay Record No Expire',
            'status' => 'Status',
            'status_text' => 'Status Text',
            'is_pay' => 'Is Pay',
            'is_pay_text' => 'Is Pay Text',
            'line_name' => 'Line Name',
            'line_sequence' => 'Line Sequence',
            'driver_id' => 'Driver ID',
            'driver_name' => 'Driver Name',
            'delivery_date' => 'Delivery Date',
            'delivery_time_detail' => 'Delivery Time Detail',
            'delivery_time_id' => 'Delivery Time ID',
            'is_send_to_home' => 'Is Send To Home',
            'address_lng' => 'Address Lng',
            'address_lat' => 'Address Lat',
            'address_name' => 'Address Name',
            'address_district' => 'Address District',
            'address' => 'Address',
            'address_detail' => 'Address Detail',
            'receive_name' => 'Receive Name',
            'receive_tel' => 'Receive Tel',
            'freight_price' => 'Freight Price',
            'reduction_price' => 'Reduction Price',
            'remark' => 'Remark',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'nick_name' => 'Nick Name',
            'close_time' => 'Close Time',
            'close_type' => 'Close Type',
            'coupon_id' => 'Coupon ID',
            'is_audit' => 'Is Audit',
            'audit_time' => 'Audit Time',
            'audit_user_id' => 'Audit User ID',
            'source' => 'Source',
            'source_txt' => 'Source Txt',
            'return_num' => 'Return Num',
            'return_price' => 'Return Price',
            'create_time' => 'Create Time',
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
        return $this->hasMany(CusOrderDetail::className(), ['order_id' => 'id']);
    }
}
