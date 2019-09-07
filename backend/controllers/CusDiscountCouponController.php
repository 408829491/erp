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
 * Class CusDiscountCouponByStoreManagerController
 * @package backend\controllers
 */
class CusDiscountCouponController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum = 0, $pageSize = 10)
    {
        $query = new CusDiscountCouponForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, "ok", $query->findPage2($pageNum, $pageSize, Yii::$app->request->get('filterProperty')));
    }

    public function actionCreate()
    {
        // 查询所有店铺
        $data = CusStore::find()->all();
        return $this->render('create', ['storeData' => $data]);
    }

    public function actionSave(){
        $saveData = new CusDiscountCoupon();
        $saveData->attributes = Yii::$app->request->post();

        if (!$saveData->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $saveData->save();

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $model = CusDiscountCoupon::findOne($id);

        // 查询所有店铺
        $data = CusStore::find()->all();
        return $this->render('update', [
            'model' => $model,
            'storeData' => $data
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
