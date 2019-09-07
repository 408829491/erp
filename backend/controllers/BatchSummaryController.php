<?php

namespace backend\controllers;
use app\models\form\BatchSummaryForm;
use backend\responses\ApiResponse;
use yii\web\Controller;

class BatchSummaryController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 订单汇总列表
     * @return mixed
     */
    public function actionList(){
        $model  = new BatchSummaryForm();
        return new ApiResponse(200,'ok',$model->getOrderSummaryList());
    }
}
