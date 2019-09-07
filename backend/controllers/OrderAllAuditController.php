<?php

namespace backend\controllers;

use app\models\form\OrderForm;
use app\models\form\PurchaseForm;
use app\models\Purchase;
use app\models\PurchaseDetail;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class OrderAllAuditController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 批量保存价格
     * @return ApiResponse
     */
    public function actionSavePrice(){
        $model = new OrderForm();
        if($model->orderAllAudit()){
            return (new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, 'false', []));
    }

}
