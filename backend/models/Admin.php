<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bn_admin".
 *
 * @property int $id
 * @property string $username
 * @property string $nickname 用户昵称
 * @property string $head_pic 用户头像
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $store_id C端管理员的店铺id
 * @property int $created_at
 * @property int $updated_at
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'nickname', 'auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'store_id', 'created_at', 'updated_at'], 'integer'],
            [['username', 'nickname', 'auth_key'], 'string', 'max' => 32],
            [['head_pic', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
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
            'head_pic' => 'Head Pic',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'store_id' => 'Store ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
