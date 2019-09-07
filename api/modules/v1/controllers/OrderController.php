<?php

namespace api\modules\v1\controllers;


use app\models\form\OrderForm;
use app\models\Order;

class OrderController extends Controller
{

    public function actionIndex(){
        $model = Order::find()->all();
        return $model;
    }

    /**
     * 订单列表
     * @return array
     */
    public function actionOrderList(){
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->search();
    }

    /**
     * 获取订单详情
     * @return mixed
     */
    public function  actionOrderDetail(){
        $orderId = \Yii::$app->request->get('order_id');
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->getOrderDetail($orderId);
    }

    /**
     * 订单下单
     * @return array
     */
    public function actionSave(){
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->save();
    }

    /**
     * 获取用户常购清单
     * @return mixed
     */
    public function actionGetPurchaseList(){
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->getUserOrderCommodity();
    }


    /**
     * 订单统计
     * @return mixed
     */
    public function actionOrderStatistics(){
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->getOrderStatistics();
    }

    /**
     * 用户订单状态统计
     * @return mixed
     */
    public function actionOrderStatus(){
        $model = new OrderForm();
        $model->user = $this->user;
        return $model->getOrderStatus();
    }

}