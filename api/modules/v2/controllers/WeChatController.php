<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/4/20
 * Time: 16:32
 */

namespace api\modules\v2\controllers;

use abei2017\wx\Application;
use abei2017\wx\core\Exception;
use app\models\CusCommodity;
use app\models\CusGroupOrder;
use app\models\CusMember;
use app\models\CusOrder;
use app\models\FinanceBalance;
use app\models\form\CusFinanceForm;
use app\models\form\CusSyncForm;
use app\models\form\FinanceForm;
use app\models\Order;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use common\models\UserCus;
use Yii;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

class WeChatController extends Controller
{
    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {

        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'optional' => [
                    'register',
                    'register-by-phone'
                ]
            ],
        ]);
    }

    /**
     * 获取AccessToken值
     * @return mixed
     */
    public function actionGetAccessToken()
    {
        $at = (new Application())->driver("mini2.accessToken");
        $token = $at->getToken();
        return $token;
    }


    /**
     * 生成小程序码或二维码
     * $extra 附加属性数组，width、auto_color、line_color
     * @param $path
     * @param $type
     * @return mixed
     */
    public function actionGetQrode($path, $type = 0)
    {
        $QRCode = (new Application())->driver("mini2.qrcode");
        $scene = '';
        if ($type == 0) {
            return $QRCode->forever($path, $extra = []);
        }
        return $QRCode->unLimit($scene, $path, $extra = []);
    }

    /**
     * 注册用户通过手机号
     * @return array|ApiResponse
     */
    public function actionRegisterByPhone()
    {
        $code = Yii::$app->request->post('code');
        $encryptedData = Yii::$app->request->post('encryptedData');
        $iv = Yii::$app->request->post('iv');

        if ($code == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $user = (new Application())->driver("mini2.user");
        $result = $user->codeToSession($code);

        // openid已经存在直接返回结果
        $memberData = CusMember::find()->asArray()
            ->where(['openid' => $result['openid']])
            ->one();
        if ($memberData != null) {
            return ['openid' => $memberData['openid'], 'username' => $memberData['username'], 'access_token' => $memberData['access_token']];
        }

        // 解密数据
        $data = self::decryptData($result['session_key'], $iv, $encryptedData);

        $phone = $data['phoneNumber'];
        $nickname = '会员'.time();

        // 创建用户
        $model = new UserCus();
        $model->openid = $result['openid'];
        $model->access_token = $model->generateAccessToken(time());

        // 从银豹同步会员
        $cusSyncForm = new CusSyncForm();
        $syncData = $cusSyncForm->syncGetUser($phone);
        if ($syncData['status'] == 'success' && isset($syncData['data'])) {
            // 银豹中存在，拉取余额、积分、UUID保存
            $model->uid = $syncData['data']['customerUid'];
            $model->balance = $syncData['data']['balance'];
            $model->integral = $syncData['data']['point'];

            CusMember::deleteAll(['username' => $phone]);
        } else {
            // 银豹中加入用户
            $syncData2 = $cusSyncForm->syncAddUser(['number' => $phone, 'name' => $nickname, 'phone' => $phone]);
            $model->uid = $syncData2['data']['customerUid'];
        }

        // 添加附加信息
        $model->username = $phone;
        $model->mobile = $phone;
        $model->is_advanced = 1;
        $model->head_pic = 'https://smart-escort-bed.oss-cn-beijing.aliyuncs.com/file/face-url-default-img.png';
        $model->nickname = $nickname;
        $model->sex = 1;

        $model->save();

        return ['openid' => $result['openid'], 'access_token' => $model->access_token];
    }

    /**
     * 注册用户
     * @return array|ApiResponse
     */
    public function actionRegister()
    {
        $code = Yii::$app->request->post('code');
        /*$encryptedData = Yii::$app->request->post('encryptedData');*/
        /*$iv = Yii::$app->request->post('iv');*/
        $rawData = Yii::$app->request->post('rawData');

        if ($code == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $user = (new Application())->driver("mini2.user");
        $result = $user->codeToSession($code);

        // openid已经存在直接返回结果
        $memberData = CusMember::find()->asArray()
            ->where(['openid' => $result['openid']])
            ->one();
        if ($memberData != null) {
            return ['openid' => $memberData['openid'], 'username' => $memberData['username'], 'access_token' => $memberData['access_token']];
        }

        // 解密数据
        /*$data = self::decryptData($result['session_key'], $iv, $encryptedData);*/
        // 创建用户
        $model = new UserCus();
        $model->username = $result['openid'];
        $model->openid = $result['openid'];
        $model->access_token = $model->generateAccessToken(time());

        // 添加附加信息
        $rawData = json_decode($rawData);
        $model->head_pic = $rawData->avatarUrl;
        $model->nickname = $rawData->nickName;
        $model->sex = $rawData->gender;

        $model->save();

        return ['openid' => $result['openid'], 'access_token' => $model->access_token];
    }

    /**
     * 获取用户 openid、session_key、unionid
     * @param $code
     * @return mixed
     */
    public function actionGetUserInfo($code)
    {
        $user = (new Application())->driver("mini2.user");
        $result = $user->codeToSession($code);
        return $result;
    }

    /**
     * 发送模板消息
     * @return array|string
     */
    public function actionSendTemplateMsg($openId = 'omkbI5a2vf7xDv_59n3wp0dEQSPQ', $templateId = 'fgl7hTA5SFgf__uPcwTYY_NB5LGH1LphFkU3ByTHP60', $data)
    {
        $template = (new Application())->driver("mini2.template");
        $data = [
            'keyword1' => array(
                'value' => "abc",
            ),
            'keyword2' => array(
                'value' => date('Y-m-d H:i:s', time()),
            ),
        ];
        try {
            $response = $template->send($openId, $templateId, $formId = 1, $data, $extra = []);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return ['code' => 200, 'msg' => 'ok', 'data' => $response];
    }

    /**
     * 在线充值
     * @param $amount
     * @param $openId
     * @return array
     */
    public function actionMiniPay()
    {
        $amount = Yii::$app->request->post('amount');
        $payment = (new Application())->driver("mini2.pay");
        //生成订单
        $openId = $this->user['openid'];
        $financeForm = new CusFinanceForm();
        //生成交易流水
        $data = [
            'price' => $amount,
            'balance' => $this->user['balance'],
            'user_id' => $this->user['id'],
            'user_name' => $this->user['username'],
            'op_user' => $this->user['username'],
            'pay_user' => $this->user['nickname'],
            'order_no' => '',
            'nick_name' => $this->user['nickname'],
        ];
        if ($refer_no = $financeForm->savesBalance($data, 0)) {
            $attributes = [
                'body' => "充值",
                'out_trade_no' => $refer_no,
                'total_fee' => $amount * 100,
                'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['v2/we-chat/notify']),
                'openid' => $openId
            ];
            $jsApi = $payment->jsApi($attributes);
            if ($jsApi->return_code == 'SUCCESS' && $jsApi->result_code == 'SUCCESS') {
                $prepayId = $jsApi->prepay_id;
                $result = $payment->configForPayment($prepayId);
                $result['order_no'] = $refer_no;
                return ['code' => 200, 'msg' => 'ok', 'data' => $result];
            }
        }
        return ['code' => 400, 'msg' => '支付失败', 'data' => []];
    }

    /**
     * 订单支付
     */
    public function actionWxPayByOrderId()
    {
        // 如果是余额支付校验支付密码是否正确
        $pay_money_way = Yii::$app->request->post('pay_money_way');
        if ($pay_money_way == 0) {
            $payPassword = Yii::$app->request->post('payPassword');
            if ($payPassword != $this->user['pay_password']) {
                return new ApiResponse(ApiCode::CODE_ERROR, '您输入的支付密码不正确');
            }
        }

        //根据id查询订单
        $id = Yii::$app->request->post('id');
        $model = CusOrder::findOne($id)->toArray();
        $balance = $this->user['balance'];
        $model['balance'] = $balance;
        if ('Y' === $model['is_pay']) {
            return ['code' => 400, 'msg' => '订单已结算，请不要重复支付', 'data' => []];
        }
        if ('0' === $pay_money_way) {
            if ($balance < $model['pay_price']) {
                return ['code' => 400, 'msg' => '余额不足', 'data' => []];
            }
            $financeForm = new CusFinanceForm();
            //生成结算单
            $financeForm->save($model,1);
            //生成交易流水
            if ($financeForm->savesBalance($model)) {
                $financeForm->updateUserAccount($this->user['id'], $model['price'] * -1);
                return ['code' => 200, 'msg' => '余额支付成功', 'data' => []];
            }
            return ['code' => 400, 'msg' => '余额支付失败', 'data' => []];
        }

        // 去下单
        $payment = (new Application())->driver("mini2.pay");
        $attributes = [
            'body' => "订单下单",
            'out_trade_no' => $model['order_no'],
            'total_fee' => $model['pay_price'] * 100,
            'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['v2/we-chat/notify']),
            'openid' => $this->user['openid']
        ];
        $jsApi = $payment->jsApi($attributes);
        if ($jsApi->return_code == 'SUCCESS' && $jsApi->result_code == 'SUCCESS') {
            $prepayId = $jsApi->prepay_id;
            $result = $payment->configForPayment($prepayId);
            $result['id'] = $id;
            return ['code' => 200, 'msg' => 'ok', 'data' => $result];
        }
        return ['code' => 400, 'msg' => '订单支付失败', 'data' => []];
    }


    /**
     * 订单支付
     */
    public function actionWxPayByGroupOrderId()
    {
        // 如果是余额支付校验支付密码是否正确
        $pay_money_way = Yii::$app->request->post('pay_money_way');
        if ($pay_money_way == 0) {
            $payPassword = Yii::$app->request->post('payPassword');
            if ($payPassword != $this->user['pay_password']) {
                return new ApiResponse(ApiCode::CODE_ERROR, '您输入的支付密码不正确');
            }
        }

        //根据id查询订单
        $id = Yii::$app->request->post('id');
        $model = CusGroupOrder::findOne($id)->toArray();
        $balance = $this->user['balance'];
        $model['balance'] = $balance;
        if ('Y' === $model['is_pay']) {
            return ['code' => 400, 'msg' => '订单已结算，请不要重复支付', 'data' => []];
        }
        if ('0' === $pay_money_way) {
            if ($balance < $model['pay_price']) {
                return ['code' => 400, 'msg' => '余额不足', 'data' => []];
            }
            $financeForm = new CusFinanceForm();
            //生成结算单
            $financeForm->save($model,2);
            //生成交易流水
            if ($financeForm->savesBalance($model)) {
                $financeForm->updateUserAccount($this->user['id'], $model['price'] * -1);
                return ['code' => 200, 'msg' => '余额支付成功', 'data' => []];
            }
            return ['code' => 400, 'msg' => '余额支付失败', 'data' => []];
        }

        // 去下单
        $payment = (new Application())->driver("mini2.pay");
        $attributes = [
            'body' => "拼团订单下单",
            'out_trade_no' => $model['order_no'],
            'total_fee' => $model['pay_price'] * 100,
            'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['v2/we-chat/notify']),
            'openid' => $this->user['openid']
        ];
        $jsApi = $payment->jsApi($attributes);
        if ($jsApi->return_code == 'SUCCESS' && $jsApi->result_code == 'SUCCESS') {
            $prepayId = $jsApi->prepay_id;
            $result = $payment->configForPayment($prepayId);
            $result['id'] = $id;
            return ['code' => 200, 'msg' => 'ok', 'data' => $result];
        }
        return ['code' => 400, 'msg' => '订单支付失败', 'data' => []];
    }

    /**
     * 支付成功通知
     * @return mixed
     */
    public function actionNotify()
    {
        $pay = (new Application())->driver("mini2.pay");
        $response = $pay->handleNotify(function ($notify, $isSuccess) {
            if ($isSuccess) {
                @list($type, $id, $_) = explode('-', $notify['out_trade_no']);
                //file_put_contents('notify.txt',json_encode($notify));
                $financeForm = new CusFinanceForm();
                if ('DD' === substr($notify['out_trade_no'], 0, 2)) {
                    $order = CusOrder::findOne(['order_no' => $notify['out_trade_no']]);
                    //更新客户积分
                    $financeForm->updateCusUserIntegral(1, $order['user_id']);
                    //生成结算单
                    $financeForm->save($order, 1);
                } else if ('GD' === substr($notify['out_trade_no'], 0, 2)) {
                    $order = CusGroupOrder::findOne(['order_no' => $notify['out_trade_no']]);
                    //更新客户积分
                    $financeForm->updateCusUserIntegral(1, $order['user_id']);
                    //生成结算单
                    $financeForm->save($order, 2);
                } else if ('RE' === substr($notify['out_trade_no'], 0, 2)) {
                    //更新客户余额
                    $balance = FinanceBalance::findOne(['recharge_no' => $notify['out_trade_no']]);
                    $shopServer = new CusSyncForm();
                    $user = UserCus::findOne($balance['user_id'])->toArray();
                    $totalFee = $notify['total_fee'] / 100;
                    $info = $shopServer->updateBalancePoint($user['uid'], $totalFee, 0);
                    $shopUserInfo = $shopServer->syncUser($user['username']);//查询远程门店用户信息
                    if (isset($shopUserInfo['data']['balance']) && $shopUserInfo['data']['balance'] > 0) {
                        $totalFee = ($notify['total_fee'] / 100) + $shopUserInfo['data']['balance'];
                        $financeForm->updateCusUserAccountReplace($balance['user_id'], $totalFee);
                    } else {
                        $totalFee = $notify['total_fee'] / 100;
                        $financeForm->updateCusUserAccount($balance['user_id'], $totalFee);
                    }

                    //更新流水单状态
                    $financeForm->updateBalanceState($notify['out_trade_no']);
                }
                return true;
            }
        });
        return $response;
    }

    /**
     * 企业付款到零钱
     * @param array $params
     */
    public function actionMchPay($params = [])
    {
        $mch = (new Application())->driver("mp.mch");
        $mch->send($params);
    }

    /**
     * 查询企业付款
     * @param $partnerTradeNo商户订单号
     * @return mixed
     */
    public function actionQueryMchPay($partnerTradeNo)
    {
        $mch = (new Application())->driver("mp.mch");
        $result = $mch->query($partnerTradeNo);
        return $result;
    }

    /**
     * 发送现金红包
     * @param $params
     */
    public function actionSendRedPacket($params)
    {
        $redPacket = (new Application())->driver("mp.redbag");
        $redPacket->send($params, $type = 'normal');
    }

    /**
     * 获取现金红包
     * @param $mchBillNo
     */
    public function actionGetRedPacket($mchBillNo)
    {
        $redPacket = (new Application())->driver("mp.redbag");
        $redPacket->query($mchBillNo);
    }


    /**
     * 发送客服消息
     * @param $openId
     * @param $msgType
     * @param $data
     * @return mixed
     */
    public function actionSendMsg($openId, $msgType, $data)
    {
        $message = (new Application())->driver("mini2.custom");
        return $message->send($openId, $msgType, $data);
    }


    /**
     * 解密信息
     * @param $sessionKey
     * @param $iv
     * @param $encryptedData
     * @return mixed
     */
    public function decryptData($sessionKey, $iv, $encryptedData)
    {
        $aesKey = base64_decode($sessionKey);
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result, true);
        return $dataObj;
    }


}