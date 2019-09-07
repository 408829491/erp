<?php

namespace api\modules\v3\controllers;

use app\models\form\DeliveryTaskForm;

class DeliveryTaskController extends Controller
{

    /**
     * 配送任务列表
     * @return array
     */
    public function actionTaskList()
    {
        $model = new DeliveryTaskForm();
        $model->user = $this->user;
        return $model->getTaskList();
    }

    /**
     * 报告计单异常状态
     * @return mixed
     */
    public function actionReportOrderException($id,$status)
    {
        $model = new DeliveryTaskForm();
        $model->user = $this->user;
        return $model->reportOrderException($id,$status);
    }

    /**
     * 配送完成
     * @return mixed
     */
    public function actionConfirmDelivery($id)
    {
        $model = new DeliveryTaskForm();
        $model->user = $this->user;
        return $model->confirmDelivery($id);
    }

    /**
     * 订单状态统计
     * @return mixed
     */
    public function actionOrderStatistic(){
        $model = new DeliveryTaskForm();
        $model->user = $this->user;
        return $model->getOrderStatistic();
    }

}