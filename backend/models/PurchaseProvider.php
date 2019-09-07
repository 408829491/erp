<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_purchase_provider".
 *
 * @property int $id ID
 * @property string $account 登录账号
 * @property string $password 登录密码
 * @property string $name 供应商名称
 * @property string $mobile 联系人手机
 * @property string $contact_name 联系人
 * @property string $tel 联系人电话
 * @property string $address_detail 详细地址
 * @property string $invoice_number 税号
 * @property string $invoice_title 发票抬头
 * @property string $pic 资质图片
 * @property string $bank 开户行
 * @property string $bank_account 银行账户
 * @property string $bank_name 开户名称
 * @property int $is_delete 是否删除
 * @property int $create_time 创建时间
 */
class PurchaseProvider extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_purchase_provider';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account', 'name'], 'required'],
            [['pic'], 'string'],
            [['is_delete', 'create_time'], 'integer'],
            [['account', 'password', 'invoice_number', 'invoice_title', 'bank', 'bank_account', 'bank_name'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 150],
            [['mobile', 'contact_name', 'tel'], 'string', 'max' => 50],
            [['address_detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => '登录账号',
            'password' => '登录密码',
            'name' => '供应商名称',
            'mobile' => '联系人手机',
            'contact_name' => '联系人',
            'tel' => '联系人电话',
            'address_detail' => '详细地址',
            'invoice_number' => '税号',
            'invoice_title' => '发票抬头',
            'pic' => '资质图片',
            'bank' => '开户行',
            'bank_account' => '银行账户',
            'bank_name' => '开户名称',
            'is_delete' => '是否删除',
            'create_time' => '创建时间',
        ];
    }
}
