<?php
namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property string $id
 * @property string $username
 * @property string $openid
 * @property string $nickname
 * @property string $head_pic
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $access_token
 * @property string $mobile
 * @property integer $status
 * @property integer $is_check
 * @property integer $created_at
 * @property string $created_address
 * @property string $created_ip
 * @property integer $last_login_date
 * @property string $last_login_ip
 * @property string $last_login_address
 * @property string $source
 * @property integer $integral
 * @property string $balance
 * @property integer $updated_at
 * @property string $address
 */

class SortSorter extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['status', 'created_at', 'last_login_date', 'updated_at','is_check','type'], 'integer'],
            [['username'], 'required'],
            ['username', 'string', 'max' => 50],
            ['nickname', 'string', 'max' => 32],
            [['openid', 'head_pic', 'email'], 'string', 'max' => 255],
            ['mobile', 'unique'],
            [['access_token'], 'string', 'max' => 100],
            [['device_addr'], 'string', 'max' => 50],
            [['device_pwd'], 'string', 'max' => 30],
            [['print_num'], 'string', 'max' => 50],
            [['print_pwd'], 'string', 'max' => 30],
            [['username'],'unique'],
            [['email'], 'string', 'max' => 50],
            [['sort_id'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = static::find()->where(['access_token'=>$token , 'status' => self::STATUS_ACTIVE])->one();
        if($user){
            return $user;
        }else{
            return null;
        }
    }
    /**
     * @inheritdoc
     */
    public static function loginByAccessToken($token, $type = null)
    {
        $user = static::find()->where(['access_token'=>$token , 'status' => self::STATUS_ACTIVE])->one();
        if($user){
            return $user;
        }else{
            return null;
        }
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email){
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by mobile
     *
     * @param string $mobile
     * @return static|null
     */
    public static function findByMobile($mobile){
        return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken($expire_time)
    {
        $this->access_token = Yii::$app->security->generateRandomString().'_'.$expire_time;
        return $this->access_token;
    }
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户账号',
            'nickname' => '昵称',
            'openid' => 'openid',
            'head_pic' => '头像',
            'auth_key' => 'AuthKey',
            'password_hash' => '密码',
            'password_reset_token' => '密码重置码',
            'access_token' => 'Access Token',
            'mobile' => '手机',
            'email' => '电子邮件',
            'status' => '状态',
            'r_id' => '用户等级',
            'is_check' => '是否审核',
            'created_at' => '注册时间',
            'created_address' => '注册地点',
            'created_ip' => '注册IP',
            'last_login_date' => '最后登录时间',
            'last_login_ip' => '最后登录IP',
            'last_login_address' => '最后登录地点',
            'integral' => '积分',
            'balance' => '余额',
            'updated_at' => '修改时间',
            'address' => '收货地址',
        ];
    }

    public static function getUserName($id){
        return self::findOne($id)->username;
    }

}
