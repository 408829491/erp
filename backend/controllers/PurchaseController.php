<?php

namespace backend\controllers;

use app\models\form\PurchaseForm;
use app\models\form\PurchaseRefundForm;
use app\models\OrderDetail;
use app\models\Purchase;
use app\models\PurchaseDetail;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use PHPExcel;
use PHPExcel_IOFactory;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * PurchaseController implements the CRUD actions for Purchase model.
 */
class PurchaseController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * 获取采购单列表数据
     */
    public function actionPurchaseList()
    {
        $model = new PurchaseForm();
        $data = $model->search();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 获取采购单详情数据
     */

    public function actionPurchaseDetail($id)
    {
        $query = PurchaseDetail::find();
        $data['list'] = $query->asArray()
            ->where(['purchase_id' => $id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        foreach($data['list']  as &$v){
            $v['refund_num'] = $v['purchase_num'];
            $v['refund_price'] = $v['purchase_price'];
            $v['total_refund_price'] = $v['purchase_total_price'];
        }
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 获取采购单详情数据
     * @param $id
     * @return ApiResponse
     */
    public function actionGetPurchaseDetail($id){
        $data = $this->getData($id);
        foreach($data['details']  as &$v){
            $v['refund_num'] = $v['purchase_num'];
            $v['refund_price'] = $v['purchase_price'];
            $v['total_refund_price'] = $v['purchase_total_price'];
        }
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 创建采购单
     * @return mixed
     */
    public function actionSave()
    {
        $model = new PurchaseForm();
        $res = $model->save();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }

    /**
     * 订单汇总生成采购单
     */
    public function actionOrderToPurchase()
    {
        $model = new PurchaseForm();
        $res = $model->OrderToPurchase();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }

    /**
     * 获取采购退货单列表数据
     */
    public function actionPurchaseRefundList()
    {
        $model = new PurchaseRefundForm();
        $data = $model->search();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }

    /**
     * 采购收货
     * @param $purchase_id
     * @return ApiResponse
     */
    public function actionFinish($purchase_id)
    {
        $model = new PurchaseForm();
        if ($res = $model->updatePurchaseStatus($purchase_id, 3)) {
            return new ApiResponse('200', 'ok');
        } else
            return new ApiResponse('0', 'false', $res);
    }


    /**
     * 创建采购单
     * @return string
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 采购单修改页面
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = $this->getData($id);
        return $this->render('update', $model);
    }


    /**
     * 采购单详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->getData($id);
        return $this->render('view', $model);
    }


    /**
     * 获取商品详情
     * @param $id
     * @return mixed
     */
    public function getData($id)
    {
        $query = Purchase::find()
            ->with([
                'details' => function ($query) {
                    $query->select('*,commodity_name as name')
                        ->andWhere('is_delete=0');
                },
            ])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        foreach ($query['details'] as &$v) {
            $v['pic'] = explode(':;', $v['pic'])[0];
        }
        return $query;
    }

    /**
     * 关闭采购单
     * @param Purchase_id
     * @return ApiResponse
     */
    public function actionClosePurchase($purchase_id)
    {
        $model = new PurchaseForm();
        if ($res = $model->updatePurchaseStatus($purchase_id, 4)) {
            return new ApiResponse('200', 'ok');
        } else
            return new ApiResponse('0', 'false', $res);
    }

    /**
     * 获取采购类型
     * @return ApiResponse
     */
    public function actionPurchaseType()
    {
        $model = new PurchaseForm();
        return $model->getPurchaseType();
    }


    /**
     * 获取采购单打印数据
     * @return mixed
     */
    public function actionGetPrintData($id, $type = 0)
    {
        $model = new PurchaseForm();
        return new ApiResponse('200', 'ok', $model->getPrintData($id, $type));
    }

    /**
     * 查看指定供应商的采购单web界面（适配手机端）
     */
    public function actionProviderPurchaseView()
    {
        $id = \Yii::$app->request->get('id');

        //$id = PurchaseForm::authCode($id, 'decode');

        $purchase = Purchase::findOne($id);

        // 查询子表
        $purchaseDetail = PurchaseDetail::find()->asArray()
            ->select('group_concat(commodity_id) as commodityIds')
            ->where(['purchase_id' => $id])
            ->one();

        // 查询订单明细根据子表ids跟orderIds
        $orderDataList = OrderDetail::find()->asArray()
            ->select('bn_order_detail.*, bn_order.user_id, bn_order.nick_name')
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->where(['in', 'bn_order_detail.order_id', explode(',', $purchase->order_ids)])
            ->andWhere(['in', 'bn_order_detail.commodity_id', explode(',', $purchaseDetail['commodityIds'])])
            ->all();
        // 根据商品聚合
        $commodityDataList = [];
        $commodityData = [];
        foreach ($orderDataList as $item) {
            $flag = true;
            foreach ($commodityDataList as &$item2) {
                if ($item['commodity_id'] == $item2['commodity_id'] && $item['unit'] == $item2['unit']) {
                    $flag = false;
                    $commodityData = &$item2;
                    break;
                }
            }

            if ($flag) {
                // 不存在
                $commodityData['commodity_id'] = $item['commodity_id'];
                $commodityData['commodity_name'] = $item['commodity_name'];
                $commodityData['unit'] = $item['unit'];
                $commodityData['num'] = $item['num'];
                $commodityData['details'] = [];
                array_push($commodityData['details'], $item);
                array_push($commodityDataList, $commodityData);
            } else {
                // 已经存在叠加数量
                $commodityData['num'] = $commodityData['num'] + $item['num'];
                // 查找details中是否存在，存在数量叠加
                $flag2 = true;
                foreach ($commodityData['details'] as &$item3) {
                    if ($item3['user_id'] == $item['user_id']) {
                        $flag2 = false;
                        $item3['num'] += $item['num'];
                        break;
                    }
                }

                if ($flag2) {
                    // 不存在
                    array_push($commodityData['details'], $item);
                }
            }
        }

        // 根据供应商聚合
        $userDataList = [];
        $userData = [];
        foreach ($orderDataList as $item) {
            $flag = true;
            foreach ($userDataList as &$item2) {
                if ($item['user_id'] == $item2['user_id']) {
                    $flag = false;
                    $userData = &$item2;
                    break;
                }
            }

            if ($flag) {
                // 不存在
                $userData['user_id'] = $item['user_id'];
                $userData['nick_name'] = $item['nick_name'];
                $userData['num'] = $item['num'];
                $userData['details'] = [];
                array_push($userData['details'], $item);
                array_push($userDataList, $userData);
            } else {
                // 已经存在叠加数量
                $userData['num'] = $userData['num'] + $item['num'];
                // 查找details中是否存在不存在添加，存在数量累加
                $flag2 = true;
                foreach ($userData['details'] as &$item3) {
                    if ($item3['commodity_id'] == $item['commodity_id'] && $item3['unit'] == $item['unit']) {
                        $flag2 = false;
                        $item3['num'] += $item['num'];
                        break;
                    }
                }

                if ($flag2) {
                    array_push($userData['details'], $item);
                }
            }
        }

        return $this->render('providerPurchaseView', ['model' => $purchase, 'type' => $purchase->purchase_type, 'commodityDataList' => $commodityDataList, 'userDataList' => $userDataList]);
    }

    /** 分享采购单
     * @return mixed
     */
    public function actionSharePurchase($id)
    {
        $model = new PurchaseForm();
        return $this->render('share', $model->getPurchase($id));
    }

    /**
     * 进价历史
     * @param $id
     * @return string
     */
    public function actionPurchasePriceHistory()
    {
        return $this->render('history');
    }


    /**
     * 获取进价历史数据
     * @param $id
     * @return ApiResponse
     */
    public function actionGetPurchasePriceHistoryData($id)
    {
        $model = new PurchaseForm();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getPurchasePriceHistory($id)));

    }

    /**
     * 导出采购单
     * @param $id
     */
    public function actionExportPurchase($id)
    {
        $model = new PurchaseForm();
        $model->exportPurchase($id);
    }

    /**
     * Finds the Purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $source
     * @param string $source_txt
     * @param integer $create_time
     * @return Purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Purchase::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
