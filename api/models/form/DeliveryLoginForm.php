<?php
namespace api\models\form;

use common\models\UserDelivery;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class DeliveryLoginForm extends Model
{
    public $username;
    public $password;
    public $mobile;
    public $shop_name;
    public $address;
    public $invite_code;
    public $password_hash;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return mixed
     */
    public function login()
    {
        $user = $this->getUser();
        if ($this->validate()) {
            $accessToken = $user->generateAccessToken(time()+2400);
			//下面更新用户登录相关信息
			$user->last_login_date = time();
			$user->last_login_ip = Yii::$app->request->getRemoteIP();
			$user->access_token = $accessToken;
            if($user->save()){
                return  ['user'=>$user,'accessToken'=>$accessToken];
            }
            return  false;
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = UserDelivery::findByUsername($this->username);
        }
        return $this->_user;
    }

}
