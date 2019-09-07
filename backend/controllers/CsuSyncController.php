<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/6/15
 * Time: 16:47
 */

namespace backend\controllers;


use app\models\CusCommodity;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;

class CsuSyncController extends Controller
{
    private $appKey = "485722344603534746";
    private $appId = "4BB1340841B02759DB60510D4E3EC96E";

    /**
     * 获取门店商品列表
     * @param $maxId
     */
    public function actionSyncCommodity($maxId = 0)
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
        return json_encode($list);
    }

    /**
     * 创建商品
     * @param $maxId
     */
    public function actionSyncAddCommodity()
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
     * 创建用户
     * @param $maxId
     */
    public function actionSyncAddUser($userData)
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
    public function actionSyncGetUser($mobile)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/customerOpenApi/queryByNumber";
        $data = [
            "appId" => $this->appId,
            "customerNum" => $mobile
        ];
        return $this->initRequest($url, $data);
    }

    /**
     * 获取门店订单列表
     * @param $data
     */
    public function actionSyncOrder($maxId = 0)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/ticketOpenApi/queryTicketPages";
        $data = [
            "appId" => $this->appId,
            "startTime" => "2019-6-17 00:00:00",
            "endTime" => "2019-6-17 24:00:00",
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => "12251980"//分页ID，取自上次访问ID
            ]
        ];
        return $this->initRequest($url, $data);
    }


    /**
     * 获取门店订单列表
     */
    public function actionSyncAddOrder()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/orderOpenApi/addOnLineOrder";
        $data = [
            "appId" => $this->appId,
            "payMethod" => "Cash",
            "customerNumber" => "001",
            "shippingFee" => 15.00,
            "orderRemark" => "addOnLineOrder",
            "orderDateTime" => "2015-12-04 10:05:01",
            "contactAddress" => "测试测试。。。。",
            "contactName" => "张三",
            "contactTel" => "1360097865",
            "deliveryType" => 1,
            "dinnersNumber" => 5,
            "restaurantAreaName" => "一楼",
            "restaurantTableName" => "11",
            "reservationTime" => "2018-01-12 12:30:00",
            "items" => [
                "productUid" => 102066793346170331,
                "comment" => "测试添加",
                "quantity" => 1.2,
                "manualSellPrice" => 30.2
            ]
        ];
        return $this->initRequest($url, $data);
    }

    /**
     * 获取门店分类列表
     * @param $data
     */
    public function actionSyncCategory($maxId = 0)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/queryProductCategoryPages";
        $data = [
            "appId" => $this->appId,
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => $maxId//分页ID，取自上次访问ID
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