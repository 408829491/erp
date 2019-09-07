<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cus_member".
 *
 * @property string $id
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $openid 微信openid
 * @property string $login_date 登陆时间
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
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
            [['login_date', 'create_datetime', 'modify_datetime'], 'safe'],
            [['username', 'password', 'openid'], 'string', 'max' => 255],
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
            'password' => 'Password',
            'openid' => 'Openid',
            'login_date' => 'Login Date',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }
}
