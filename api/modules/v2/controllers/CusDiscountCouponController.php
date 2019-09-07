<?php

namespace api\modules\v2\controllers;

use app\models\CusDiscountCoupon;
use app\models\CusDiscountCouponGetRecord;
use app\models\CusShippingAddress;
use app\models\CusStore;
use app\utils\GeoDeUtils;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;

/**
 * C端优惠券
 */
class CusDiscountCouponController extends Controller
{
    // 领取优惠券
    public function actionAddDiscount() {
        $id = \Yii::$app->request->post('id');
        $model = CusDiscountCoupon::findOne($id);
        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, '优惠券不存在');
        }

        $model2 = new CusDiscountCouponGetRecord();
        $model2->attributes = $model->attributes;
        $model2->cus_member_id = $this->user->id;
        $model2->discount_coupon_id = $model->id;

        $model2->save();

        return new ApiResponse();
    }

    // 获取领取的优惠券并且加载可用的收货地址
    public function actionGetUsableAndAddress() {
        $storeId = \Yii::$app->request->post('storeId');
        $store = CusStore::findOne($storeId);

        if ($store == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $data = CusDiscountCouponGetRecord::find()->asArray()
            ->where(['cus_member_id' => $this->user->id, 'store_id' => $storeId])
            ->andWhere(['<=', 'start_date', date('Y-m-d')])
            ->andWhere(['>', 'end_date', date('Y-m-d')])
            ->andWhere(['<=', 'condition', \Yii::$app->request->post('totalPrice')])
            ->andWhere(['is_use' => 0])
            ->all();

        // 选出优惠金额最大的优惠券
        if (count($data) == 0) {
            $selectData = null;
        } else {
            $selectData = $data[0];
            for ($i = 1; $i < count($data); $i ++) {
                if ($data[$i]['distance'] > $selectData['distance']) {
                    $selectData = $data[$i];
                }
            }
        }

        // 获取可用的收货地址
        $selectedLocationId = \Yii::$app->request->post('selectedLocation');

        $addressList = [];
        $address = CusShippingAddress::find()
            ->where(['member_id' => $this->user->id])
            ->all();

        $index = 0;
        for ($i = 0; $i < count($address); $i ++) {
            $distance = GeoDeUtils::calculateDistance($store->lng, $store->lat, $address[$i]->lng, $address[$i]->lat);
            if ($store->limit_delivery_meter >= $distance) {
                array_push($addressList, $address[$i]);
                if ($selectedLocationId != null && $selectedLocationId == $address[$i]->id) {
                    $index = count($addressList) - 1;
                    $selectedLocationId == null;
                }
            }
        }

        return ['dataList' => $data, 'selectData' => $selectData, 'addressList' => $addressList, 'addressListIndex' => $index];
    }
}