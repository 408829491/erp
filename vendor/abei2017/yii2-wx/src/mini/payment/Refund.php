<?php
/*
 * This file is part of the abei2017/yii2-wx
 *
 * (c) abei <abei@nai8.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace abei2017\wx\mini\payment;

use abei2017\wx\core\Driver;
use Yii;
use yii\base\Response;
use abei2017\wx\core\Exception;
use yii\httpclient\Client;
use abei2017\wx\helpers\Util;
use abei2017\wx\helpers\Xml;

class Refund extends Driver
{

    /**
     * 订单退款接口地址
     */
    const PREPARE_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * Prepare
     * @var
     */
    private $prepare;

    /**
     * 生成预备订单
     * @throws Exception
     * @return object
     */
    protected function prepare($attr = [])
    {

        if (empty($attr['out_trade_no'])) {
            throw new Exception('缺少接口必填参数out_trade_no！');
        } elseif (empty($attr['out_refund_no'])) {
            throw new Exception('缺少接口必填参数out_refund_no！');
        } elseif (empty($attr['total_fee'])) {
            throw new Exception('缺少统一支付接口必填参数total_fee！');
        } elseif (empty($attr['refund_fee'])) {
            throw new Exception('缺少接口必填参数refund_fee！');
        }
        $attr['appid'] = $this->conf['app_id'];
        $attr['mch_id'] = $this->conf['payment']['mch_id'];
        $attr['nonce_str'] = Yii::$app->security->generateRandomString(32);
        $attr['sign'] = Util::makeSign($attr, $this->conf['payment']['key']);

        $response = $this->post(self::PREPARE_URL, $attr)->setFormat(Client::FORMAT_XML)->send();
        return $this->prepare = (object)$response->setFormat(Client::FORMAT_XML)->getData();
    }

    /**
     * 退款
     * @param array $attributes
     * @return object prepare
     */
    public function refund($attributes = [])
    {
        $result = $this->prepare($attributes);
        return $result;
    }

}