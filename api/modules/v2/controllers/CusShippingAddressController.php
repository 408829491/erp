<?php

namespace api\modules\v2\controllers;

use app\models\CusShippingAddress;
use app\models\CusStore;
use app\utils\GeoDeUtils;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

/**
 * C端收货地址管理
 */
class CusShippingAddressController extends Controller
{

    public function behaviors() {

        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'optional' =>[
                    'find-by-location',
                    'find-list-by-keyword'
                ]
            ],
        ]);
    }

    // 根据id删除收货地址
    public function actionDeleteById() {
        CusShippingAddress::deleteAll(['id' => \Yii::$app->request->post('id')]);

        return new ApiResponse();
    }

    // 修改收货地址
    public function actionEditAddress() {
        $model = CusShippingAddress::findOne(\Yii::$app->request->post('id'));
        $model->attributes = \Yii::$app->request->post();
        $model->save();

        return new ApiResponse();
    }

    // 根据id加载地址
    public function actionFindById() {
        $data = CusShippingAddress::findOne(\Yii::$app->request->post('id'));
        return $data;
    }

    // 加载个人所有收货地址
    public function actionFindAll() {
        $data = CusShippingAddress::find()
            ->where(['member_id' => $this->user->id])
            ->all();
        return $data;
    }

    // 根据门店id查询可用的收货地址
    public function actionFindUsableAddress() {
        $id = \Yii::$app->request->post('id');

        // 加载可用配送地址
        $storeData = CusStore::findOne($id)->toArray();
        $addressData = CusShippingAddress::find()->asArray()
            ->where(['member_id'=>\Yii::$app->user->identity['id']])
            ->all();
        $newAddressData = [];
        foreach ($addressData as &$v) {
            $distance = GeoDeUtils::calculateDistance($storeData['lng'], $storeData['lat'], $v['lng'], $v['lat']);
            if ($distance <= $storeData['limit_delivery_meter']) {
                array_push($newAddressData, $v);
            }
        }

        return $newAddressData;
    }

    // 保存收货地址
    public function actionSaveAddress() {
        $model = new CusShippingAddress();
        $model->attributes = \Yii::$app->request->post();
        $model->member_id = $this->user['id'];
        $model->save();

        return new ApiResponse();
    }

    // 根据关键字搜索相关地址列表
    public function actionFindListByKeyword() {
        $keyword = \Yii::$app->request->post('keyword');
        // 限定杭州区域
        $data = GeoDeUtils::findListByKeyword($keyword, '330100');

        // 查询所有店铺
        $storeId = \Yii::$app->request->post('storeId');
        if ($storeId == null) {
            $allStore = CusStore::find()->asArray()->all();
        } else {
            $allStore = CusStore::find()->where(['id' => $storeId])->asArray()->all();
        }

        $newData = [];
        foreach ($data as $v) {
            if ($v['district'] != null) {
                // 计算是否可以配送
                $location = explode(',', $v['location']);

                $flag = false;
                foreach ($allStore as $v2) {
                    $distance = GeoDeUtils::calculateDistance($v2['lng'], $v2['lat'], $location[0], $location[1]);
                    if ($v2['limit_delivery_meter'] >= $distance) {
                        $flag = true;
                        break;
                    }
                }

                $v['isUsable'] = $flag;
                array_push($newData, $v);
            }
        }
        return $newData;
    }

    // 加载收货地址 并筛选可用（在所有门店范围内）
    public function actionFindListByLocation() {
        $lng = \Yii::$app->request->post('lng');
        $lat = \Yii::$app->request->post('lat');

        if ($lng == null || $lat == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $data = CusShippingAddress::find()->asArray()
            ->where(['member_id'=>$this->user['id']])
            ->all();

        // 查询所有店铺
        $allStore = CusStore::find()->asArray()->all();

        $newData = [];
        foreach ($data as $v) {
            $flag = false;
            foreach ($allStore as $v2) {
                $distance = GeoDeUtils::calculateDistance($v['lng'], $v['lat'], $v2['lng'], $v2['lat']);
                if (intval($v2['limit_delivery_meter']) >= $distance) {
                    $flag = true;
                    break;
                }
            }
            if ($flag) {
                array_push($newData, $v);
            }
        }

        return $newData;
    }

    // 根据定位查询地址并查询附近的地址，并查询可用的收货地址
    public function actionFindByLocation() {
        $lng = \Yii::$app->request->post('lng');
        $lat = \Yii::$app->request->post('lat');

        if ($lng == null || $lat == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        // 获取所有门店，并筛选可用的收货地址
        $query = CusStore::find()->asArray();
        $data = $query->all();

        // 计算距离
        $flag = false;
        foreach ($data as &$v) {
            $v['distance'] = GeoDeUtils::calculateDistance($v['lng'], $v['lat'], $lng, $lat);
            // 计算是否有可用的地址
            if ($v['distance'] <= $v['limit_delivery_meter']) {
                $flag = true;
            }
        }

        // 根据坐标获取地址和poi信息
        $addressData = GeoDeUtils::getAddressAndPoi($lng, $lat);
        $formattedAddress = $addressData['regeocode']['formatted_address'];
        $district = $addressData['regeocode']['addressComponent']['district'];

        // 获取poi，最多5个
        $pois = $addressData['regeocode']['pois'];
        $len = count($pois);
        if ($len > 5) {
            $len = 5;
        }

        $poiData = [];
        for ($i = 0; $i < $len ; $i ++) {
            $tempData = $pois[$i];
            $tempData['is_usable'] = $flag;
            $poiData[$i] = $tempData;
        }

        return ['is_usable'=>$flag,'poiData'=>$poiData, 'addressData'=>substr($formattedAddress, strpos($formattedAddress, $district) + strlen($district), strlen($formattedAddress))];
    }

}