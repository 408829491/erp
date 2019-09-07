<?php

namespace backend\controllers;

use app\models\form\SetPriceForm;
use backend\responses\ApiResponse;
use yii\web\Controller;

class SetPriceController extends Controller
{
    /**
     * 同步公式定义计算的商品价格
     * @return ApiResponse
     */
    public function actionSyncSettingPrice()
    {
        $model = new SetPriceForm();
        $model->syncCommodityPrice();
        return new ApiResponse();
    }
}
