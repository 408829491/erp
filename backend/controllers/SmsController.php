<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/6/3
 * Time: 11:09
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class SmsController extends Controller
{
    /**
     * 发送验证码短信
     * @param $mobile
     * @param $code
     * @return mixed
     */
    public function actionSendSms($mobile,$code)
    {
        $response = Yii::$app->aliyun->sendSms(
            "阿里云短信测试专用", // 短信签名
            "SMS_139925354", // 短信模板编号
            $mobile, // 短信接收者
            Array(  // 短信模板中字段的值
                "code" => $code,
            ),
            "1"//选填, 发送短信流水号
        );
        return $response;
    }
}