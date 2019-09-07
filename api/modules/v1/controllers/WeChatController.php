<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/4/20
 * Time: 16:32
 */

namespace api\modules\v1\controllers;

use abei2017\wx\Application;
use abei2017\wx\core\Exception;
use app\models\FinanceBalance;
use app\models\form\FinanceForm;
use app\models\Order;
use Yii;

class WeChatController extends Controller
{
    public function init()
    {
        parent::init();
    }

    /**
     * 获取AccessToken值
     * @return mixed
     */
    public function actionGetAccessToken()
    {
        $at = (new Application())->driver("mini.accessToken");
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
        $QRCode = (new Application())->driver("mini.qrcode");
        $scene = '';
        if ($type == 0) {
            return $QRCode->forever($path, $extra = []);
        }
        return $QRCode->unLimit($scene, $path, $extra = []);
    }


    /**
     * 获取用户 openid、session_key、unionid
     * @param $code
     * @return mixed
     */
    public function actionGetUserInfo($code)
    {
        $user = (new Application())->driver("mini.user");
        $result = $user->codeToSession($code);
        return $result;
    }


    /**
     * 发送模板消息
     * @return array|string
     */
    public function actionSendTemplateMsg()
    {
        $template = (new Application())->driver("mini.template");
        $data = [];
        try {
            $template->send($toUser = 1, $templateId = '', $formId = 1, $data, $extra = []);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return ['code' => 200, 'msg' => 'ok'];
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
        $payment = (new Application())->driver("mini.pay");
        //生成订单
        $openId = $this->user['openid'];
        $financeForm = new FinanceForm();
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
                'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['v1/we-chat/notify']),
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
        //根据id查询订单
        $id = Yii::$app->request->post('id');
        $pay_money_way = Yii::$app->request->post('pay_money_way');
        $model = Order::findOne($id)->toArray();
        $balance = $this->user['balance'];
        $model['balance'] = $balance;
        if ('Y' === $model['is_pay']) {
            return ['code' => 400, 'msg' => '订单已结算，请不要重复支付', 'data' => []];
        }
        if ('0' === $pay_money_way) {
            if ($balance < $model['price']) {
                return ['code' => 400, 'msg' => '余额不足', 'data' => []];
            }
            $financeForm = new FinanceForm();
            //生成结算单
            $financeForm->save($model);
            //生成交易流水
            if ($financeForm->savesBalance($model)) {
                $financeForm->updateUserAccount($this->user['id'], $model['price']*-1);
                return ['code' => 200, 'msg' => '余额支付成功', 'data' => []];
            }
            return ['code' => 400, 'msg' => '余额支付失败', 'data' => []];
        }

        // 去下单
        $payment = (new Application())->driver("mini.pay");
        $attributes = [
            'body' => "订单下单",
            'out_trade_no' => $model['order_no'],
            'total_fee' => $model['price'] * 100,
            'notify_url' => Yii::$app->urlManager->createAbsoluteUrl(['v1/we-chat/notify']),
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
        $pay = (new Application())->driver("mini.pay");
        $response = $pay->handleNotify(function ($notify, $isSuccess) {
            if ($isSuccess) {
                @list($type, $id, $_) = explode('-', $notify['out_trade_no']);
                //file_put_contents('notify.txt',json_encode($notify));
                $financeForm = new FinanceForm();
                if ('DD' === substr($notify['out_trade_no'], 0, 2)) {
                    $order = Order::findOne(['order_no' => $notify['out_trade_no']]);
                    //生成结算单
                    $financeForm->save($order);
                } else if ('RE' === substr($notify['out_trade_no'], 0, 2)) {
                    //更新客户余额
                    $balance = FinanceBalance::findOne(['recharge_no' => $notify['out_trade_no']]);
                    $financeForm->updateUserAccount($balance['user_id'], $notify['total_fee'] / 100);
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


}