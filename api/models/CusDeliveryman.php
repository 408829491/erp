<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_cus_deliveryman".
 *
 * @property string $id ID
 * @property int $store_id 店铺ID
 * @property string $username 用户名
 * @property string $nickname 姓名
 * @property string $openid openid
 * @property string $head_pic 用户头像
 * @property string $auth_key 认证key
 * @property string $password_hash 密码hash
 * @property string $password_reset_token 重置密码凭据
 * @property string $access_token 用户访问数据凭证
 * @property string $mobile 手机号码
 * @property int $status 用户状态
 * @property int $r_id 用户等级
 * @property string $created_address 注册账号的地点
 * @property string $created_ip 注册账号的IP
 * @property string $delivery_time 送货时间
 * @property string $line_name 线路名称
 * @property int $is_check 是否审核
 * @property string $area_name 区域
 * @property int $is_deleted 是否删除
 * @property int $updated_at 更新时间
 * @property int $created_at 注册账号时间
 */
class CusDeliveryman extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_deliveryman';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'status', 'r_id', 'is_check', 'is_deleted', 'updated_at', 'created_at'], 'integer'],
            [['username', 'updated_at', 'created_at'], 'required'],
            [['username', 'nickname', 'auth_key'], 'string', 'max' => 32],
            [['openid', 'head_pic', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 100],
            [['mobile'], 'string', 'max' => 11],
            [['created_address'], 'string', 'max' => 200],
            [['created_ip'], 'string', 'max' => 15],
            [['delivery_time', 'line_name', 'area_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => '店铺ID',
            'username' => '用户名',
            'nickname' => '姓名',
            'openid' => 'openid',
            'head_pic' => '用户头像',
            'auth_key' => '认证key',
            'password_hash' => '密码hash',
            'password_reset_token' => '重置密码凭据',
            'access_token' => '用户访问数据凭证',
            'mobile' => '手机号码',
            'status' => '用户状态',
            'r_id' => '用户等级',
            'created_address' => '注册账号的地点',
            'created_ip' => '注册账号的IP',
            'delivery_time' => '送货时间',
            'line_name' => '线路名称',
            'is_check' => '是否审核',
            'area_name' => '区域',
            'is_deleted' => '是否删除',
            'updated_at' => '更新时间',
            'created_at' => '注册账号时间',
        ];
    }
}
