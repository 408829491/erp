<?php

namespace api\modules\v2\controllers;


use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\form\CusOrderForm;

class CusOrderController extends Controller
{

    public function actionIndex(){
        $model = CusOrder::find()->all();
        return $model;
    }

    /**
     * 订单列表
     * @return array
     */
    public function actionOrderList(){
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->search();
    }

    /**
     * 获取订单详情
     * @return mixed
     */
    public function  actionOrderDetail(){
        $orderId = \Yii::$app->request->get('order_id');
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->getOrderDetail($orderId);
    }

    /**
     * 订单下单
     * @return array
     */
    public function actionSave(){
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->save();
    }

    /**
     * 获取用户常购清单
     * @return mixed
     */
    public function actionGetPurchaseList(){
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->getUserOrderCommodity();
    }


    /**
     * 订单统计
     * @return mixed
     */
    public function actionOrderStatistics(){
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->getOrderStatistics();
    }

    /**
     * 确认收货
     * @param $order_id
     * @return mixed
     */
    public function actionConfirmReceive($order_id){
        $model = new CusOrderForm();
        return $model->updateOrderStatus($order_id,4);
    }

    /**
     * 用户订单状态统计
     * @return mixed
     */
    public function actionOrderStatus(){
        $model = new CusOrderForm();
        $model->user = $this->user;
        return $model->getOrderStatus();
    }

    // 根据订单id查询订单明细
    public function actionFindOrderDetailById() {
        $dataList = CusOrderDetail::find()->asArray()
            ->where(['order_id' => \Yii::$app->request->post('orderId')])
            ->all();

        foreach ($dataList as &$item) {
            $item['evaluateIndex'] = 2;
        }

        return $dataList;
    }

}