<?php

namespace backend\controllers;

use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\CommodityProfile;
use app\models\DeliveryLine;
use app\models\OrderDetail;
use app\models\form\OrderForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use app\models\Order;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class StoreRoomController extends Controller
{

    public function actionIndex()
    {
        //商品类别
        $commodityCategory = CommodityCategory::find()->where(['pid' => 0])->all();
        return $this->render('index',['commodityCategory' => $commodityCategory]);
    }


    /**
     * 获取入库单数据
     */
    public function actionOrderList()
    {
        $model = new OrderForm();
        $data = $model->search();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 获取入库单详情数据
     */
    public function actionOrderDetail($id)
    {
        $query = OrderDetail::find();
        $data['list'] = $query->asArray()
            ->where(['order_id' => $id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 保存入库单详情商品信息
     */
    public function actionSaveOrderDetail()
    {
        $model = new Order();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
            } else {
                return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors));
            }
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors));
    }


    /**
     * 创建入库单
     * @return mixed
     */
    public function actionSave()
    {
        $model = new OrderForm();
        $res = $model->save();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }

    /**
     * 入库完成
     * @param $order_id
     * @return ApiResponse
     */
    public function actionFinish($order_id)
    {
        $model = new OrderForm();
        if ($res = $model->updateOrderStatus($order_id, 3)) {
            return new ApiResponse('200', 'ok');
        } else
            return new ApiResponse('0', 'false', $res);
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 入库单修改页面
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = new OrderForm();
        return $this->render('update', $model->getData($id));
    }

    /**
     * 退货页面
     * @param $id
     * @return string
     */
    public function actionRefund($id)
    {
        $model = new OrderForm();
        return $this->render('refund', $model->getData($id));
    }


    /**
     * 入库单详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = new OrderForm();
        return $this->render('view', $model->getData($id));
    }


    /**
     * 入库单核算
     * @param $id
     * @return string
     */
    public function actionApproval($id)
    {
        $model = new OrderForm();
        return $this->render('approval', $model->getData($id));
    }


    /**
     * 关闭入库单
     * @param $order_id
     * @return ApiResponse
     */
    public function actionCloseOrder($order_id)
    {
        $model = new OrderForm();
        if ($res = $model->updateOrderStatus($order_id, 4)) {
            return new ApiResponse('200', 'ok');
        } else
            return new ApiResponse('0', 'false', $res);
    }

    /**
     * 实始化model
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 客户下单情况
     */
    public function actionOrderState()
    {
        return $this->render('orderState');
    }

    /**
     * 获取客户订单统计数据
     */
    public function actionGetOrderStateData()
    {
        $model = new OrderForm();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getUserOrderData()));
    }

    /**
     * 客户下单列表
     * @param $id
     * @return string
     */
    public function actionUserOrderList($id)
    {
        return $this->render('userOrderList', ['id' => $id]);
    }

    /**
     * 客户下单列表数据
     * @param $user_id
     * @return ApiResponse
     */
    public function actionGetUserOrderListData($user_id)
    {
        $model = new OrderForm();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->GetUserOrderListData($user_id)));
    }

    /**
     * 客户订货历史
     * @param $id
     * @return string
     */
    public function actionHistory()
    {
        return $this->render('history');
    }

    /**
     * 获取订单商品汇总数据
     * @return ApiResponse
     */
    public function actionOrderCommodityListData($id = 0)
    {
        $model = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getOrderCommodityList($id));
    }

    /**
     * 获取订单商品汇总详情数据
     * @return ApiResponse
     */
    public function actionOrderCommodityDetailData($user_id = 0, $id = 0)
    {
        $model = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getOrderCommodityDetail($user_id, $id));
    }


    public function actionGetPrintData($id){
        $model = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getPrintData($id));
    }

    /**
     * 期初库存
     * @return string
     */
    public function actionEarlyRepertory(){
        return $this->render('earlyRepertory');
    }

    /**
     * 出库管理
     * @return string
     */
    public function actionDeliveryList(){

        return $this->render('deliveryList');
    }

    /**
     * 发货出库（按客户）
     * @return string
     */
    public function actionOutOfStock(){
        // 查询所有路线
        $lineData = DeliveryLine::find()->all();
        return $this->render('outOfStock', ['lineData' => $lineData]);
    }

    /**
     * 发货出库（按订单）
     * @return string
     */
    public function actionOutOfStockByOrder(){
        // 查询所有路线
        $lineData = DeliveryLine::find()->all();
        return $this->render('outOfStockByOrder', ['lineData' => $lineData]);
    }

    /**
     * 发货出库（按客户）数据
     * @return string
     */
    public function actionOutOfStockData(){
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->outOfStockData(Yii::$app->request->get('filterProperty')));
    }

    /**
     * 发货出库（按订单）数据
     * @return string
     */
    public function actionOutOfStockByOrderData(){
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->outOfStockByOrderData(Yii::$app->request->get('filterProperty')));
    }

    // 发货出库（按客户）数据详情
    public function actionOutOfStockDataView(){
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->outOfStockDataView(Yii::$app->request->get('userId'), Yii::$app->request->get('deliveryDate')));
    }

    // 发货出库（按订单）数据详情
    public function actionOutOfStockByOrderDataView(){
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->outOfStockByOrderDataView(Yii::$app->request->get('orderId')));
    }

    // 按照客户跟日期进行出库操作
    public function actionSendOutByDateAndUserId() {
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->sendOutByDateAndUserId(Yii::$app->request->get('date'), Yii::$app->request->get('userId')));
    }

    // 按照订单进行出库操作
    public function actionSendOutByOrderId() {
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->sendOutByOrderId(Yii::$app->request->get('orderId')));
    }

    // 按照日期进行批量出库
    public function actionSendOutByDate() {
        $orderForm = new OrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $orderForm->sendOutByDate(Yii::$app->request->get('date')));
    }

    /**
     * 库存盘点
     * @return string
     */
    public function actionInventoryCheckList(){
        return $this->render('inventoryCheckList');
    }

    /**
     * 报损报溢
     * @return string
     */
    public function actionReportLPList(){
        return $this->render('reportLPList');
    }

    /**
     * 现有库存
     * @return string
     */
    public function actionExistingStockList(){
        return $this->render('existingStockList');
    }

    /**
     * 成本变更记录
     * @return string
     */
    public function actionChangeRecorder(){
        return $this->render('changeRecorder');
    }

    /**
     * 修改上下限制View
     * @return string
     */
    public function actionSetTop(){
        $id = Yii::$app->request->get('id');
        $commodity = CommodityProfile::find()
            ->select('bn_commodity.name as commodity_name,bn_commodity_profile.name as unit,bn_commodity_profile.stock_limit_up_num,,bn_commodity_profile.stock_limit_down_num')
            ->where(['bn_commodity_profile.id'=>$id])
            ->leftJoin('bn_commodity','bn_commodity.id=bn_commodity_profile.commodity_id')
            ->asArray()
            ->one();
        return $this->render('setTop', $commodity);
    }

    // 保存上下限制
    public function actionSetTopSave() {
        $id = Yii::$app->request->post('id');
        $unit = Yii::$app->request->post('unit');
        $commodity = CommodityProfile::find()->where(['commodity_id'=>$id,'name'=>$unit,])->one();
        $commodity->attributes = Yii::$app->request->post();
        $commodity->save();
        return new ApiResponse();
    }
}
