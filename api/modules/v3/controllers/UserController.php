<?php

namespace api\modules\v3\controllers;

use api\models\form\DeliveryLoginForm;
use yii;

class UserController extends Controller
{
    const C_MANAGER = 'C端管理员';
    const C_DRIVER='门店配送员';
    const B_DRIVER='配送员';
    /**
     * 用户登录
     * @return mixed
     */
    public function actionLogin ()
    {
        $model = new DeliveryLoginForm;
        $model->setAttributes(Yii::$app->request->post());
        $info = $model->login();
        if ($info) {
            if(0 === $info['user']['is_check']){
                return ['code'=>400,'msg'=>'审核中，请稍候再试','data'=>[]];
            }
            $auth = Yii::$app->authManager;
            $role = array_keys($auth->getRolesByUser($info['user']['id']));
            if(in_array(self::C_DRIVER,$role)){
                return ['access_token' => $info['accessToken'],'role'=>self::C_DRIVER];
            }else if(in_array(self::C_MANAGER,$role)){
                return ['access_token' => $info['accessToken'],'role'=>self::C_MANAGER];
            }
            else if(in_array(self::B_DRIVER,$role)){
                return ['access_token' => $info['accessToken'],'role'=>self::B_DRIVER];
            }
            return ['code'=>400,'msg'=>'账号没有权限','data'=>[]];
        }
        else {
            return ['code'=>400,'msg'=>'用户名或密码错误','data'=>[]];
        }
    }


    /**
     * 获取用户信息
     * @return mixed
     */
    public function actionUserInfo(){
        return $this->user;
    }

}