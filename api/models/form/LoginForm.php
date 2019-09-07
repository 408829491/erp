<?php
namespace api\models\form;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
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
            $user->save();
            return  ['user'=>$user,'accessToken'=>$accessToken];
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }


    /**
     * 客户申请
     * @return array
     */
    public function userApply(){
        $model = new User();
        $model->attributes = $this->attributes = Yii::$app->request->get();
        if ($model->validate()) {
            $model->generateAuthKey();
            $model->setPassword($this->password);
            $model->created_ip = Yii::$app->getRequest()->getUserIP();
            $model->created_at = time();
            $model->contact_name = $model->nickname;
            $model->status = 10;
            $model->r_id = 3;
            if($model->save()){
                return ['code' =>200,'msg'=>'success','data'=>[]];
            }
        }
        return ['code' =>0,'msg'=>'false','data'=>$model->getErrors()];
    }
}
