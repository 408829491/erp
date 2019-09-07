<?php

namespace backend\controllers;
use app\models\CusCommodity;
use app\models\CusCommodityCategory;
use app\models\CusDiscountCoupon;
use app\models\CusStore;
use app\models\form\CusDiscountCouponForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

/**
 * 门店
 * Class CusDiscountCouponByStoreManagerController
 * @package backend\controllers
 */
class CusDiscountCouponByStoreManagerController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum = 0, $pageSize = 10)
    {
        $query = new CusDiscountCouponForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, "ok", $query->findPage($pageNum, $pageSize, Yii::$app->request->get('filterProperty')));
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionSave(){
        $saveData = new CusDiscountCoupon();
        $saveData->attributes = Yii::$app->request->post();
        $saveData->store_id = Yii::$app->user->identity['store_id'];

        $storeData = CusStore::findOne($saveData->store_id);

        if ($saveData == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        } else {
            $saveData->store_name = $storeData->name;
        }

        if (!$saveData->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $saveData->save();

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $model = CusDiscountCoupon::findOne($id);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionEdit() {

        $id = Yii::$app->request->post('id');
        $model = CusDiscountCoupon::findOne($id);
        $model->attributes = Yii::$app->request->post();

        if (!$model->validate()) {
            new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $model->save();

        return new ApiResponse();
    }

    public function actionDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids == null) {
            return new ApiResponse(ApiCode::CODE_ERROR,"失败");
        }
        $ids2 = explode(",",$ids);

        foreach ( $ids2 as $id) {
            CusDiscountCoupon::deleteAll(['id' => $id]);
        }
        return new ApiResponse();
    }
}
