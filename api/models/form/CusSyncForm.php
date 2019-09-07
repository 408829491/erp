<?php

namespace app\models\form;

use app\models\CusCommodity;
use common\models\UserCus;
use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class CusSyncForm extends Model
{
    private $appKey = "485722344603534746";
    private $appId = "4BB1340841B02759DB60510D4E3EC96E";

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
        ];
    }

    /**
     * 创建用户
     * @param $userData
     * @return string
     */
    public function syncAddUser($userData)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/customerOpenApi/add";
        $data = [
            "appId" => $this->appId,
            "customerInfo" => [
                "number" => $userData['number'],
                "name" => $userData['name'],
                "phone" => $userData['phone'],
            ]
        ];
        return $this->initRequest($url, $data);
    }

    /**
     * 查询用户信息
     * @param $maxId
     */
    public function syncGetUser($mobile)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/customerOpenApi/queryByNumber";
        $data = [
            "appId" => $this->appId,
            "customerNum" => $mobile
        ];
        return $this->initRequest($url, $data);
    }


    /**
     * 同步会员数据
     */
    public function syncUser()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/customerOpenApi/queryCustomerPages";
        $data = [
            "appId" => $this->appId,
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => ''//分页ID，取自上次访问ID
            ]
        ];
        $list = $this->initRequest($url, $data);
        if ($list['status'] == 'success') {
            foreach ($list['data']['result'] as $v) {
                $uid = number_format($v['customerUid'], 0, '', '');
                $model = UserCus::findOne(['username' => $uid]);
                if (!$model) {
                    $model = new UserCus();
                    $model->uid = $uid;
                }
                $model->username = $v['name'];
                $model->nickname = $v['number'];
                $model->mobile = $v['phone'];
                $model->source = '小程序';
                $model->balance = $v['balance'];
                $model->updated_at = $model->created_at = time();
                $model->save();
            }
            return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => $list];
        }
        return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure'];
    }


    /**
     * 更新余额和积分
     * @param $uid
     * @param $balance
     * @param $point
     * @return string
     */
    public function updateBalancePoint($uid,$balance,$point)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/customerOpenApi/updateBalancePointByIncrement";
        $data = [
            "appId" => $this->appId,
            "customerUid" => $uid,
            "balanceIncrement" => $balance,
            "pointIncrement" => $point,
            "dataChangeTime" => date('Y-m-d H:i:s',time())
        ];
        return $this->initRequest($url, $data);
    }


    /**
     * 获取门店商品列表
     * @param $maxId
     */
    public function syncCommodity($maxId = 0)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/queryProductPages";
        $data = [
            "appId" => $this->appId,
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => $maxId,//分页ID，取自上次访问ID
            ]
        ];
        $list = $this->initRequest($url, $data);
        if ($list['status'] == 'success') {
            foreach ($list['data']['result'] as $v) {
                $uid = number_format($v['uid'], 0, '', '');

                $model = CusCommodity::findOne($uid);
                if (!$model) {
                    $model = new CusCommodity();
                    $model->id = $uid;
                }
                $model->store_id = Yii::$app->user->identity['store_id'];
                $model->name = $v['name'];
                $model->type_id = number_format($v['categoryUid'], 0, '', '');
                $model->price = $v['sellPrice'];
                $model->in_price = $v['buyPrice'];
                $model->sell_stock = $v['stock'];
                $model->notice = $v['description'];
                $model->commodity_code = $v['barcode'];
                $model->is_online = $v['enable'];
                $model->pinyin = $v['pinyin'];
                $model->provider_id = $v['supplierUid'];
                $unitList = [
                    "unit_unit" => "斤",
                    "unit_is_basics_unit" => "1",
                    "unit_base_self_ratio" => "0",
                    "unit_desc" => "",
                    "unit_price" => $v['buyPrice'],
                    "unit_is_sell" => true
                ];
                $model->saveData($model, [json_encode($unitList)]);
            }
        }
        return $list;
    }

    /**
     * 创建商品
     * @param $maxId
     */
    public function syncAddCommodity()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/addProductInfo";
        $data = [
            "appId" => $this->appId,
            "productInfo" => [
                "categoryUid" => '1559529732350551821',
                "name" => "小马哥商品2",
                "barcode" => "bgtssp003",
                "buyPrice" => 10,
                "sellPrice" => 15,
                "stock" => 100,
                "pinyin" => "bgtssp",
                "description" => "新增商品测试",
                "isCustomerDiscount" => 1,
                "enable" => 1,
            ]
        ];
        return $this->initRequest($url, $data);
    }

    /**
     * 初始化远程访问请求
     * @param $url
     * @param $data
     * @return string
     */
    public function initRequest($url, $data)
    {
        $client = new Client(['baseUrl' => $url]);
        $signature = $this->signature($data);
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            ->setHeaders(['content-type' => 'application/json; charset=utf-8'])
            ->addHeaders(['user-agent' => 'openApi'])
            ->addHeaders(['accept-encoding' => 'gzip,deflate'])
            ->addHeaders(['time-stamp' => time()])
            ->addHeaders(['data-signature' => $signature])
            ->setData($data)
            ->send();
        $responseData = $response->getData();
        return $responseData;
    }

    /**
     * 生成签名
     * @param $data
     * @return string
     */
    public function signature($data)
    {
        return strtoupper(md5($this->appKey . json_encode($data)));
    }
}
