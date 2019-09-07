<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/6/3
 * Time: 11:09
 */

namespace api\modules\v2\controllers;

use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use yii\web\Response;

class SmsController extends Controller
{
    /**
     * 发送验证码短信
     * @param $mobile
     * @return mixed
     */
    public function actionSendSms()
    {
        $mobile = Yii::$app->request->post('mobile');
        if ($mobile == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $code=rand(1001,9999);
        $response = Yii::$app->aliyun->sendSms(
            "游兔", // 短信签名
            "SMS_139925354", // 短信模板编号
            $mobile, // 短信接收者
            Array(  // 短信模板中字段的值
                "code" => $code,
                "product" => 'rbc'
            ),
            "1"//选填, 发送短信流水号
        );
        if ($response['code'] == 200) {
            //验证码存储session,有效时间5分钟
            Yii::$app->cache->set($mobile, $code, 60 * 5);
            return ['code' => $response['code'], 'msg' => '短信发送成功'];
        }
        return $response;
    }


    /**
     * 订单下单短信通知
     * @return mixed
     */
    public function actionSendOrderMsg(){
        return Yii::$app->cache->get('13866665747');
    }
}