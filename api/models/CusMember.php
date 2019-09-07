<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_member".
 *
 * @property string $id ID
 * @property string $username 用户名
 * @property string $nickname 用户昵称
 * @property string $openid openid
 * @property string $head_pic 用户头像
 * @property string $auth_key 认证key
 * @property string $password_hash 密码hash
 * @property string $password_reset_token 重置密码凭据
 * @property string $access_token 用户访问数据凭证
 * @property string $mobile 手机号码
 * @property string $email 用户电子邮箱
 * @property int $status 用户状态
 * @property int $r_id 用户等级
 * @property int $sex 性别(1男，2女)
 * @property string $created_address 注册账号的地点
 * @property string $created_ip 注册账号的IP
 * @property int $last_login_date 最后一次登录时间
 * @property string $last_login_ip 最后一次登录IP
 * @property string $last_login_address 最后一次登录地点
 * @property int $integral 积分
 * @property string $business_license 营业执照
 * @property string $c_type 客户类型
 * @property string $shop_name 店铺名称
 * @property string $address 收货地址
 * @property string $delivery_time 送货时间
 * @property string $line_name 线路名称
 * @property string $balance 余额
 * @property string $invite_code 邀请码
 * @property int $is_check 是否审核
 * @property string $receive_mobile 收货手机
 * @property string $contact_name 联系人
 * @property int $is_pay_on 是否支持货到付款
 * @property string $sale_man 业务员
 * @property int $sale_man_id 业务员ID
 * @property string $area_name 区域
 * @property int $is_deleted 是否删除
 * @property string $stock_name 所属仓库
 * @property string $device_addr 设备地址
 * @property string $device_pwd 设备密码
 * @property string $print_num 打印机序号
 * @property string $print_pwd 打印机密码
 * @property string $sort_id 分拣ID
 * @property int $is_advanced 是否开通高级会员
 * @property string $pay_password 支付密码
 * @property int $updated_at 更新时间
 * @property int $created_at 注册账号时间
 */
class CusMember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'updated_at', 'created_at'], 'required'],
            [['status', 'r_id', 'sex', 'last_login_date', 'integral', 'is_check', 'is_pay_on', 'sale_man_id', 'is_deleted', 'is_advanced', 'updated_at', 'created_at'], 'integer'],
            [['balance','uid'], 'number'],
            [['sort_id'], 'string'],
            [['username', 'nickname', 'auth_key'], 'string', 'max' => 32],
            [['openid', 'head_pic', 'password_hash', 'password_reset_token', 'email', 'pay_password'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 100],
            [['mobile'], 'string', 'max' => 11],
            [['created_address', 'last_login_address'], 'string', 'max' => 200],
            [['created_ip', 'last_login_ip'], 'string', 'max' => 15],
            [['business_license', 'address'], 'string', 'max' => 999],
            [['c_type', 'receive_mobile', 'contact_name', 'sale_man', 'stock_name'], 'string', 'max' => 30],
            [['shop_name'], 'string', 'max' => 150],
            [['delivery_time', 'line_name', 'invite_code', 'area_name', 'device_addr', 'device_pwd', 'print_num', 'print_pwd'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nickname' => 'Nickname',
            'openid' => 'Openid',
            'head_pic' => 'Head Pic',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'access_token' => 'Access Token',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'status' => 'Status',
            'r_id' => 'R ID',
            'sex' => 'Sex',
            'created_address' => 'Created Address',
            'created_ip' => 'Created Ip',
            'last_login_date' => 'Last Login Date',
            'last_login_ip' => 'Last Login Ip',
            'last_login_address' => 'Last Login Address',
            'integral' => 'Integral',
            'business_license' => 'Business License',
            'c_type' => 'C Type',
            'shop_name' => 'Shop Name',
            'address' => 'Address',
            'delivery_time' => 'Delivery Time',
            'line_name' => 'Line Name',
            'balance' => 'Balance',
            'invite_code' => 'Invite Code',
            'is_check' => 'Is Check',
            'receive_mobile' => 'Receive Mobile',
            'contact_name' => 'Contact Name',
            'is_pay_on' => 'Is Pay On',
            'sale_man' => 'Sale Man',
            'sale_man_id' => 'Sale Man ID',
            'area_name' => 'Area Name',
            'is_deleted' => 'Is Deleted',
            'stock_name' => 'Stock Name',
            'device_addr' => 'Device Addr',
            'device_pwd' => 'Device Pwd',
            'print_num' => 'Print Num',
            'print_pwd' => 'Print Pwd',
            'sort_id' => 'Sort ID',
            'is_advanced' => 'Is Advanced',
            'pay_password' => 'Pay Password',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
