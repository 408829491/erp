<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/6/15
 * Time: 16:47
 */

namespace api\modules\v2\controllers;


use app\models\CusCommodity;
use app\models\CusCommodityCategory;
use app\models\CusMember;
use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\CusStore;
use app\models\form\CusCommodityForm;
use common\models\UserCus;
use Yii;
use yii\db\Exception;
use yii\httpclient\Client;

class CsuSyncController extends Controller
{
    private $appKey;
    private $appId;
    private $storeId;
    private $storeName;
    private $parameterValue = 0;

    public function init()
    {
        parent::init();
        $config = $this->getStoreConfig(Yii::$app->request->get('store_id'));
        if (isset($config) && $config['is_sync'] === '1') {
            $this->appId = $config['app_id'];
            $this->appKey = $config['app_key'];
            $this->storeId = $config['id'];
            $this->storeName = $config['name'];
        }
    }

    /**
     * 获取商品数据
     * @return array
     */
    public function actionSyncCommodity()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/queryProductPages";
        $requestTime = time();

        $data = [
            "appId" => $this->appId,
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => $this->parameterValue //分页ID，取自上次访问ID
            ]
        ];
        $list = $this->initRequest($url, $data);

        return ['requestTime' => date('Y-m-d H:i:s', $requestTime), 'endTime' => date('Y-m-d H:i:s', time()), 'status' => $list];
    }


    /**
     * 同步商品数据
     * @return array
     * @throws \Exception
     */
    public function actionSyncCommodityData()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/queryProductPages";
        $requestTime = time();
        do {
            $data = [
                "appId" => $this->appId,
                "postBackParameter" => [
                    "parameterType" => "LAST_RESULT_MAX_ID",
                    "parameterValue" => $this->parameterValue //分页ID，取自上次访问ID
                ]
            ];
            $list = $this->initRequest($url, $data);
            if ($list['status'] == 'success') {
                foreach ($list['data']['result'] as $v) {
                    $uid = $v['uid'];
                    $model = CusCommodity::findOne(['commodity_code' => $v['barcode'],'store_id'=>$this->storeId]);
                    if (!$model) {
                        $model = new CusCommodity();
                        $model->pic = 'https://smart-escort-bed.oss-cn-beijing.aliyuncs.com/file/fault_commodity_img.png';
                    }
                    $category = CusCommodityCategory::findOne(['id'=>$v['categoryUid'],'store_id'=>$this->storeId]);
                    $model->uid = $uid;
                    $model->store_id = $this->storeId;
                    $model->name = $v['name'];
                    $model->type_id = $v['categoryUid'];
                    $model->type_first_tier_id = $category['pid'];
                    $model->price = $v['sellPrice'];
                    $model->in_price = $v['buyPrice'];
                    $model->sell_stock = $v['stock'];
                    //$model->notice = $v['description'];
                    $model->unit = '斤';
                    $model->commodity_code = $v['barcode'];
                    //$model->is_online = $v['enable'];
                    $model->pinyin = $v['pinyin'];
                    $model->provider_id = $v['supplierUid'];
                    $unitList = [
                        "unit_unit" => "斤",
                        "unit_is_basics_unit" => "1",
                        "unit_base_self_ratio" => "0",
                        "unit_desc" => "",
                        "unit_price" => $v['sellPrice'],
                        "unit_is_sell" => true
                    ];
                    $form = new CusCommodityForm();
                    $form->saveData($model, [json_encode($unitList)]);
                }
                $this->parameterValue = $list['data']['postBackParameter']['parameterValue'];
            } else {
                return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure'];
            }
        } while ($this->parameterValue);
        return ['requestTime' => date('Y-m-d H:i:s', $requestTime), 'endTime' => date('Y-m-d H:i:s', time()), 'status' => $list];
    }


    /**
     * 获取门店商品列表
     * @param $maxId
     * @return mixed
     */
    public function actionSyncCommodityList($maxId = 0)
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
        return $list;
    }

    /**
     * 创建商品
     * @return mixed
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
     * 获取门店订单列表
     * @param $maxId
     * @return mixed
     */
    public function actionSyncOrder($maxId = 0)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/ticketOpenApi/queryTicketPages";
        $data = [
            "appId" => $this->appId,
            "startTime" => date('Y-m-d', time()) . " 00:00:00",
            "endTime" => date('Y-m-d', time()) . " 24:00:00",
            "postBackParameter" => [
                "parameterType" => "LAST_RESULT_MAX_ID",
                "parameterValue" => "12251980"//分页ID，取自上次访问ID
            ]
        ];
        return $this->initRequest($url, $data);
    }


    /**
     * 同步门店订单数据
     * @return mixed
     */
    public function actionSyncOrderData()
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/ticketOpenApi/queryTicketPages";
        $requestTime = time();
        do {
            $data = [
                "appId" => $this->appId,
//                "startTime" => date('Y-m-d', time()) . " 00:00:00",
//                "endTime" => date('Y-m-d', time()) . " 24:00:00",
                "startTime" => "2019-07-26 00:00:00",
                "endTime" => "2019-07-26 24:00:00",
                "postBackParameter" => [
                    "parameterType" => "LAST_RESULT_MAX_ID",
                    "parameterValue" => $this->parameterValue //分页ID，取自上次访问ID
                ]
            ];
            $list = $this->initRequest($url, $data);
            if ($list['status'] == 'success') {
                foreach ($list['data']['result'] as $v) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $model = CusOrder::findOne(['order_no' => $v['sn'],'store_id'=>$this->storeId]);
                        if (!$model) {
                            $model = new CusOrder();
                        }
                        $model->order_no = $v['sn'];
                        $model->user_id = $v['uid'];
                        $model->store_id = $this->storeId;
                        $model->store_name = $this->storeName;
                        $model->user_name = $v['cashier']['name'];
                        $model->nick_name = $v['cashier']['name'];
                        $model->price = $v['totalAmount'];
                        $model->pay_price = $v['payments'][0]['amount'];
                        $model->total_profit = $v['totalProfit'];
                        $model->quantity = $v['totalProfit'];
                        $model->status = 3;
                        $model->status_text = '已完成';
                        $model->is_pay = 'Y';
                        $model->pay_way_text = '已支付';
                        $model->source_txt = '门店';
                        $model->delivery_date = $v['datetime'];
                        $model->delivery_time_detail = '7:00~9:00';
                        $model->create_time = strtotime($v['datetime']);
                        if ($model->save()) {
                            $this->insertData($v['items'], $model);
                        } else {
                            var_dump($model->getErrors());
                        }
                        $transaction->commit();
                    } catch (Exception $e) {
                        $transaction->rollBack();
                        return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure', 'data' => $e->getMessage()];
                    }
                }
                $this->parameterValue = $list['data']['postBackParameter']['parameterValue'];
            } else {
                return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure'];
            }
        } while ($this->parameterValue);
        return ['requestTime' => date('Y-m-d H:i:s', $requestTime), 'endTime' => date('Y-m-d H:i:s', time()), 'status' => 'success'];
    }


    /**
     * 插入订单相关数据
     * @param $item
     * @param $order
     * @throws Exception
     */
    private function insertData($item, $order)
    {
        foreach ($item as $v) {

            $_model = CusOrderDetail::findOne(['order_id' => $order->id, 'commodity_id' => $v['productUid']]);
            if (!$_model) {
                $_model = new CusOrderDetail();
            }
            $_model->store_id = $this->storeId;
            $_model->order_id = $order->id;
            $_model->commodity_id = $v['productUid'];
            $_model->commodity_name = $v['name'];
            $_model->source_type_id = 3;//来源ID
            $_model->num = $v['quantity'];
            $_model->price = $v['buyPrice'];
            $_model->in_price = $v['sellPrice'];
            $_model->product_code = isset($v['productBarcode']) ? $v['productBarcode'] : '';
            $_model->unit = '斤';
            $_model->total_price = $v['totalAmount'];
            $_model->total_profit = $v['totalProfit'];
            $_model->create_time = time();
            if (!$_model->save()) {
                throw new Exception(json_encode($_model->getErrors()));
            }
        }
    }

    /**
     * 新增门店订单
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
     * 同步会员数据
     */
    public function actionSyncUserData()
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
                $uid = $v['customerUid'];
                $model = UserCus::findOne(['uid' => $uid]);
                if (!$model) {
                    $model = new UserCus();
                    $model->uid = $uid;
                }
                $model->username = $v['number'];
                $model->nickname = $v['name'];
                $model->mobile = $v['phone'];
                $model->balance = $v['balance'];
                $model->updated_at = $model->created_at = time();
                $model->save();
            }
            return $list;
        }
        return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure'];
    }


    /**
     * 创建用户
     * @param $userData
     * @return mixed
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
     * 获取门店分类列表
     * @param $maxId
     * @return mixed
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
     * 同步门店分类数据
     * @param $maxId
     * @return mixed
     */
    public function actionSyncCategoryData($maxId = 0)
    {
        $url = "https://area11-win.pospal.cn/pospal-api2/openapi/v1/productOpenApi/queryProductCategoryPages";
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
                $uid = $v['uid'];
                $model = CusCommodityCategory::findOne(['uid' => $v['uid'],'store_id'=>$this->storeId]);
                if (!$model) {
                    $model = new CusCommodityCategory();
                    $model->uid = $uid;
                }
                $model->store_id = $this->storeId;
                $model->id = $uid;
                $model->name = $v['name'];
                $model->pid = $v['parentUid'];
                $model->level = 1;
                $model->is_delete = 0;
                $model->save();
                var_dump($model->getErrors());
            }
            return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => $list];
        }
        return ['requestTime' => date('Y-m-d H:i:s', time()), 'status' => 'failure'];
    }

    /**
     * @param $url
     * @param $data
     * @return mixed
     */
    public function initRequest($url, $data)
    {
        if ($this->appId && $this->appKey) {
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
        else{
            return ['status' => 'failure', 'msg' => '未找到同步配置', 'data' => []];
        }
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

    /**
     * 获取门店配置
     * @param $storeId
     * @return array
     */
    public function getStoreConfig($storeId)
    {
            $query = CusStore::find();
            $data = $query->select('id,name,app_id,app_key,is_sync')
                ->where(['id' => $storeId])
                ->asArray()
                ->one();
            return $data;
    }

}