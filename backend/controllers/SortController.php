<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\DeliveryLine;
use app\models\form\SortForm;
use app\models\Order;
use app\models\OrderDetail;
use app\models\PurchaseBuyer;
use app\models\PurchaseProvider;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\base\Exception;
use yii\db\Expression;
use yii\web\Controller;

class SortController extends Controller
{

    /**
     * 首页
     * @return string
     */
    public function actionIndex()
    {
        // 查询所有路线
        $lineData = DeliveryLine::find()->all();
        // 查询所有供应商
        $providerData = PurchaseProvider::find()->all();
        // 查询所有采购员
        $buyerData = PurchaseBuyer::find()->all();
        // 查询所有大类
        $commodityCategory = CommodityCategory::find()->where(['pid' => 0])->all();

        return $this->render('index', ['lineData' => $lineData, 'providerData' => $providerData, 'buyerData' => $buyerData, 'commodityCategory' => $commodityCategory]);
    }

    // 商品分拣进度
    public function actionSortRate() {
        return $this->render('sortRateIndex');
    }

    // 客户分拣进度
    public function actionSortRateByUser() {
        // 查询所有路线
        $lineData = DeliveryLine::find()->all();
        return $this->render('sortRateByUserIndex', ['lineData' => $lineData]);
    }

    /**
     * 列表数据
     */
    public function actionGetIndexData()
    {
        $model = new SortForm();
        return new ApiResponse(200, 'ok', $model->search(\Yii::$app->request->get('filterProperty')));
    }

    // 商品分拣进度数据
    public function actionSortRateData() {
        $model = new SortForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->sortRateData(\Yii::$app->request->get('filterProperty')));
    }

    // 客户分拣进度数据
    public function actionSortRateByUserData() {
        $model = new SortForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->sortRateByUserData(\Yii::$app->request->get('filterProperty')));
    }

    // 商品分拣进度详情查看
    public function actionSortRateView($commodityId, $is_basics_unit, $unit, $base_self_ratio, $delivery_date) {
        $data = OrderDetail::find()->asArray()
            ->where(['commodity_id' => $commodityId])
            ->andWhere(['is_basics_unit' => $is_basics_unit])
            ->andWhere(['unit' => $unit])
            ->andWhere(['base_self_ratio' => $base_self_ratio])
            ->andWhere(['delivery_date' => $delivery_date])
            ->all();
        return $this->render('sortRateView', ['data' => $data, 'jsonData' => json_encode($data)]);
    }

    // 客户分拣进度详情查看
    public function actionSortRateByUserView($userId, $delivery_date) {
        $data = OrderDetail::find()->asArray()
            ->select('bn_order_detail.*, bn_order.nick_name')
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->where(['bn_order.user_id' => $userId])
            ->andWhere(['bn_order_detail.delivery_date' => $delivery_date])
            ->all();
        return $this->render('sortRateByUserView', ['data' => $data, 'jsonData' => json_encode($data)]);
    }

    /**
     * 更新分拣状态
     */
    public function actionChangeStatus($id,$amount)
    {
        $model = new SortForm();
        $res = $model->changeStatus($id,$amount);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, '不能重复分拣', $res);
    }

    /**
     * 重置分拣状态
     */
    public function actionReStatus($id)
    {
        $model = OrderDetail::findOne($id);
        // 查询主表的status
        $order = Order::findOne($model->order_id);
        if ($order == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        if ($order->status != 1) {
            return new ApiResponse(ApiCode::CODE_ERROR, '订单已经被处理，无法重置分拣');
        }

        $model->is_sorted = 0;
        $model->actual_num = 0;
        $model->save();

        return new ApiResponse();
    }

    /**
     * 一键分拣
     * @return ApiResponse
     */
    public function actionSortAll(){
        OrderDetail::updateAll(['actual_num' => new Expression('num'), 'is_sorted' => 1, 'sort_id' => \Yii::$app->user->identity['id'], 'sort_name' => \Yii::$app->user->identity['nickname']], ['is_sorted' => 0, 'delivery_date' => \Yii::$app->request->post('delivery_date')]);
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok');
    }

    /**
     * 获取拣货汇总数据
     * @return ApiResponse
     */
    public function actionGetPickPrintData(){
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
    }

    /**
     * 获取标签打印数据
     * @param $id
     * @return ApiResponse
     */
    public function actionPrintData($id){
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
    }

    /**
     * 检查需要分拣的商品数量
     * @return ApiResponse
     */
    public function actionCheckOneTouchPrint(){
        $model = new SortForm();
        $data = $model->checkOneTouchPrint();
        if($data['count']>0){
            return new ApiResponse(ApiCode::CODE_SUCCESS, '将有'.$data['count'].'个标签被打印(注意：此功能会分拣并打印所有未分拣的商品)，确定全部打印？',$data);
        }
        return new ApiResponse(ApiCode::CODE_SUCCESS, '没有可打印的商品!',0);
    }

    /**
     * 批量打印
     * @return ApiResponse
     */
    public function actionOneTouchPrint(){
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
    }

    /**
     * 全屏分拣
     */
    public function actionFullScreenSort() {
        return $this->render('fullScreenSort');
    }

    /**
     * 加载需要分拣的分类、二级分类
     */
    public function actionFindClassAndProduct() {
        $model = new SortForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findClassAndProduct(\Yii::$app->request->post('filterProperty')));
    }

    /**
     * 根据二级分类查找数据
     */
    public function actionFindDataBySecondTier() {
        $model = new SortForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findDataBySecondTier(\Yii::$app->request->post('filterProperty')));
    }

    /**
     * 根据一级分类查找数据
     */
    public function actionFindDataByFirstTier() {
        $model = new SortForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findDataByFirstTier(\Yii::$app->request->post('filterProperty')));
    }

    /**
     * 全屏分拣详情
     */
    public function actionFullScreenSortDetail() {
        $commodityId = \Yii::$app->request->get('commodity_id');
        if ($commodityId == null) {
            throw new Exception('商品不能为空');
        }
        $model = new SortForm();

        $deliveryDate = \Yii::$app->request->get('deliveryDate');

        $dataList = $model->fullScreenSortDetail($commodityId, $deliveryDate, \Yii::$app->request->get('isSorted'));

        $sortedNum = 0;
        foreach ($dataList as $item) {
            if ($item['is_sorted'] == 1) {
                $sortedNum += 1;
            }
        }

        $commodity = Commodity::findOne($commodityId);

        return $this->render('fullScreenSortDetail', ['commodityName' => $commodity->name, 'selectedDate' => $deliveryDate, 'commodityId' => $commodity->id, 'data' => $dataList, 'totalNum' => count($dataList), 'sortedNum' => $sortedNum]);
    }

    /**
     * 查询一键分拣需要分拣的商品
     */
    public function actionFindSortNumAllByFirstTierClassAndDate() {
        $bigClassId = \Yii::$app->request->post('bigClassId');
        $date = \Yii::$app->request->post('date');
        $model = new SortForm();

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findSortAllNumByFirstTierClassAndDate($bigClassId, $date));
    }

    /**
     * 一键分拣需要分拣的商品根据日期与大类id
     */
    public function actionSortByDateAndBigClassId() {
        $bigClassId = \Yii::$app->request->post('bigClassId');
        $date = \Yii::$app->request->post('date');

        $sqlWhere = ['is_sorted' => 0, 'delivery_date' => $date];
        if ($bigClassId != -1) {
            $sqlWhere['type_first_tier_id'] = $bigClassId;
        }
        OrderDetail::updateAll(['actual_num' => new Expression('num'), 'is_sorted' => 1, 'sort_id' => \Yii::$app->user->identity['id'], 'sort_name' => \Yii::$app->user->identity['nickname']], $sqlWhere);

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok');
    }

    /**
     * 查询分拣数量by日期跟商品id
     */
    public function actionFindSortNumAllByDateAndCommodityId() {
        $date = \Yii::$app->request->post('date');
        $commodityId = \Yii::$app->request->post('commodityId');
        $model = new SortForm();

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findSortNumAllByDateAndCommodityId($date, $commodityId));
    }

    /**
     * 一键分拣需要分拣的商品根据日期跟商品Id
     */
    public function actionSortByDateAndCommodityId() {
        $commodityId = \Yii::$app->request->post('commodityId');
        $date = \Yii::$app->request->post('date');

        $sqlWhere = ['is_sorted' => 0, 'delivery_date' => $date, 'commodity_id' => $commodityId];
        OrderDetail::updateAll(['actual_num' => new Expression('num'), 'is_sorted' => 1, 'sort_id' => \Yii::$app->user->identity['id'], 'sort_name' => \Yii::$app->user->identity['nickname']], $sqlWhere);

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok');
    }

    /**
     * 全屏分拣选择方式
     */
    public function actionFullScreenSortTypeSelect() {
        return $this->render('fullScreenSortTypeSelect');
    }

    /**
     * 按客户全屏分拣
     */
    public function actionFullScreenSortByUser() {
        return $this->render('fullScreenSortByUser');
    }

    /**
     * 查找订单通过客户聚合
     */
    public function actionFindOrderDetailByUser() {
        $model = new SortForm();

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findOrderDetailByUser(\Yii::$app->request->post('filterProperty')));
    }

    /**
     * 按客户全屏分拣明细
     */
    public function actionFullScreenSortByUserDetail() {
        $userId = \Yii::$app->request->get('user_id');
        if ($userId == null) {
            throw new Exception('用户id不能为空');
        }
        $model = new SortForm();

        $deliveryDate = \Yii::$app->request->get('deliveryDate');

        $dataList = $model->fullScreenSortByUserDetail($userId, $deliveryDate, \Yii::$app->request->get('isSorted'));

        return $this->render('fullScreenSortByUserDetail', ['data' => $dataList, 'selectedDate' => $deliveryDate, 'userId' => $userId]);
    }

    /**
     * 一键分拣查询按客户跟日期
     */
    public function actionFindSortNumAllByDateAndUserId() {
        $date = \Yii::$app->request->post('date');
        $userId = \Yii::$app->request->post('userId');
        $model = new SortForm();

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->findSortNumAllByDateAndUserId($date, $userId));
    }

    /**
     * 分拣by日期跟客户id
     */
    public function actionSortByDateAndUserId() {
        $userId = \Yii::$app->request->post('userId');
        $date = \Yii::$app->request->post('date');

        $orders = Order::find()->where(['user_id' => $userId, 'delivery_date' => $date])->all();
        $orderIds = "";
        foreach ($orders as $item) {
            $orderIds = $orderIds.$item->id;
            $orderIds = $orderIds.',';
        }
        $orderIds = substr($orderIds, 0, strlen($orderIds) - 1);

        $sqlWhere = "is_sorted = 0 and delivery_date = '$date' and order_id in ($orderIds)";
        OrderDetail::updateAll(['actual_num' => new Expression('num'), 'is_sorted' => 1, 'sort_id' => \Yii::$app->user->identity['id'], 'sort_name' => \Yii::$app->user->identity['nickname']], $sqlWhere);

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok');
    }

}