<?php

namespace api\modules\v1\controllers;

use abei2017\wx\Application;
use api\models\form\LoginForm;
use app\models\Seckill;
use common\models\User;
use yii;

class UserController extends Controller
{
    /**
     * 用户登录
     * @return mixed
     */
    public function actionLogin ()
    {
        $model = new LoginForm;
        $model->setAttributes(Yii::$app->request->get());
        $info = $model->login();
        if ($info) {
            if(0 === $info['user']['is_check']){
                return ['code'=>400,'msg'=>'客户申请审核中，请稍候再试','data'=>[]];
            }
            // 根据code获取openid，用户绑定openid
            $user = (new Application())->driver("mini.user");
            $result = $user->codeToSession(Yii::$app->request->get('code'));
            $info['user']->openid = isset($result['openid'])?$result['openid']:'';
            $info['user']->save();
            return ['access_token' => $info['accessToken']];
        }
        else {
            $model->validate();
            return $model;
        }
    }


    /**
     * 获取用户信息
     * @return mixed
     */
    public function actionUserInfo(){
        return $this->user;
    }

    /**
     * 客户申请
     * @return array
     */
    public function actionApply(){
          $model = new LoginForm();
          return $model->userApply();
    }

    /**
     * 修改用户信息
     */
    public function actionUpdate() {
        $model = $this->user;
        $shopName = Yii::$app->request->post('shop_name');
        $address = Yii::$app->request->post('address');
        if ($shopName != null) {
            $model->shop_name = $shopName;
        } else if ($address != null) {
            $model->address = $address;
        }
        $model->save();
        return;
    }

}