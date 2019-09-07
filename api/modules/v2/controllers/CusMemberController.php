<?php

namespace api\modules\v2\controllers;

use app\models\CusMember;
use app\models\form\CusSyncForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

/**
 * C端用户
 */
class CusMemberController extends Controller
{
    public function actionFindMemberInfo() {
        return ['userData' => $this->user];
    }

    // 查询手机号和是否设置支付密码
    public function actionFindIsSetPayPassword() {
        $pay_password = $this->user->pay_password;

        return ['isSetPayPassword' => $pay_password != null ? 1 : 0, 'phone' => $this->user->mobile];
    }

    // 开通会员如果绑定手机号
    public function actionOpenAdvanced() {
        $phone = Yii::$app->request->post('phone');
        $smsCode = Yii::$app->cache->get($phone);
        if ($smsCode == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, '验证码不正确');
        }
        $authCode = Yii::$app->request->post('authCode');
        if ($smsCode != $authCode) {
            return new ApiResponse(ApiCode::CODE_ERROR, '验证码不正确');
        }

        $user = CusMember::findOne($this->user->id);
        // 保存，从银豹同步会员
        $cusSyncForm = new CusSyncForm();
        $syncData = $cusSyncForm->syncGetUser($phone);
        if ($syncData['status'] == 'success' && isset($syncData['data'])) {
            // 银豹中存在，拉取余额、积分、UUID保存
            $user->uid = $syncData['data']['customerUid'];
            $user->balance = $syncData['data']['balance'];
            $user->integral = $syncData['data']['point'];

            CusMember::deleteAll(['username' => $phone]);
        } else {
            // 银豹中加入用户
            $syncData2 = $cusSyncForm->syncAddUser(['number' => $phone, 'name' => $user->nickname, 'phone' => $phone]);
            $user->uid = $syncData2['data']['customerUid'];
        }

        $user->username = $phone;
        $user->mobile = $phone;
        $user->is_advanced = 1;
        $user->save();

        Yii::$app->cache->delete($phone);
        return new ApiResponse();
    }

    // 修改用户信息
    public function actionUpdateUserInfo() {
        $member = CusMember::findOne($this->user->id);
        $member->attributes = Yii::$app->request->post();
        $member->save();

        return new ApiResponse();
    }

    // 设置支付密码
    public function actionUpdatePayPassword() {
        $phone = \Yii::$app->request->post('phone');
        $smsCode = Yii::$app->cache->get($phone);
        if ($smsCode == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, '验证码不正确');
        }
        $authCode = \Yii::$app->request->post('authCode');
        if ($smsCode != $authCode) {
            return new ApiResponse(ApiCode::CODE_ERROR, '验证码不正确');
        }
        // 保存
        $user = CusMember::findOne($this->user->id);
        $user->mobile = $phone;
        $user->pay_password = \Yii::$app->request->post('payPassword');
        $user->save();

        Yii::$app->cache->delete($phone);
        return new ApiResponse();
    }
}