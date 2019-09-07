<?php

namespace api\modules\v2\controllers;


use app\models\CusGroupOrder;
use app\models\CusGroupOrderDetail;
use app\models\form\CusGroupOrderForm;

class CusGroupOrderController extends Controller
{

    public function actionIndex(){
        $model = CusGroupOrder::find()->all();
        return $model;
    }

    /**
     * 团购订单列表
     * @return array
     */
    public function actionOrderList(){
        $model = new CusGroupOrderForm();
        $model->user = $this->user;
        return $model->search();
    }

    /**
     * 获取团购订单详情
     * @return mixed
     */
    public function  actionOrderDetail(){
        $orderId = \Yii::$app->request->get('order_id');
        $model = new CusGroupOrderForm();
        $model->user = $this->user;
        return $model->getOrderDetail($orderId);
    }

    /**
     * 团购订单下单
     * @return array
     */
    public function actionSave(){
        $model = new CusGroupOrderForm();
        $model->user = $this->user;
        return $model->save();
    }

    /**
     * 团购订单统计
     * @return mixed
     */
    public function actionOrderStatistics(){
        $model = new CusGroupOrderForm();
        $model->user = $this->user;
        return $model->getOrderStatistics();
    }

    /**
     * 用户团购订单状态统计
     * @return mixed
     */
    public function actionOrderStatus(){
        $model = new CusGroupOrderForm();
        $model->user = $this->user;
        return $model->getOrderStatus();
    }


    /**
     * 确认收货
     * @param $order_id
     * @return mixed
     */
    public function actionConfirmReceive($order_id){
        $model = new CusGroupOrderForm();
        return $model->updateOrderStatus($order_id,4);
    }

    // 根据订单id查询订单明细
    public function actionFindOrderDetailById() {
        $dataList = CusGroupOrderDetail::find()->asArray()
            ->where(['order_id' => \Yii::$app->request->post('orderId')])
            ->all();

        foreach ($dataList as &$item) {
            $item['evaluateIndex'] = 2;
        }

        return $dataList;
    }

}