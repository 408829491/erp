<?php

namespace backend\controllers;

use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\form\CusOrderForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class CusOrderByStoreManagerController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * 获取订单数据
     */
    public function actionOrderList()
    {
        $model = new CusOrderForm();
        $data = $model->search();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 获取订单详情数据
     */

    public function actionOrderDetail($id)
    {
        $query = CusOrderDetail::find();
        $data['list'] = $query->asArray()
            ->where(['order_id' => $id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 保存订单详情商品信息
     */
    public function actionSaveOrderDetail()
    {
        $model = new CusOrder();
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
     * 创建订单
     * @return mixed
     */
    public function actionSave()
    {
        $model = new CusOrderForm();
        $res = $model->save();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }

    /**
     * 订单完成
     * @param $order_id
     * @return ApiResponse
     */
    public function actionFinish($order_id)
    {
        $model = new CusOrderForm();
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
     * 订单修改页面
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = new CusOrderForm();
        return $this->render('update', $model->getData($id));
    }

    /**
     * 退货页面
     * @param $id
     * @return string
     */
    public function actionRefund($id)
    {
        $model = new CusOrderForm();
        return $this->render('refund', $model->getData($id));
    }


    /**
     * 订单详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = new CusOrderForm();
        return $this->render('view', $model->getData($id));
    }


    /**
     * 订单核算
     * @param $id
     * @return string
     */
    public function actionApproval($id)
    {
        $model = new CusOrderForm();
        return $this->render('approval', $model->getData($id));
    }


    /**
     * 关闭订单
     * @param $order_id
     * @return ApiResponse
     */
    public function actionCloseOrder($order_id)
    {
        $model = new CusOrderForm();
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
        if (($model = CusOrder::findOne(['id' => $id])) !== null) {
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
        $model = new CusOrderForm();
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
        $model = new CusOrderForm();
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
        $model = new CusOrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getOrderCommodityList($id));
    }

    /**
     * 获取订单商品汇总详情数据
     * @return ApiResponse
     */
    public function actionOrderCommodityDetailData($user_id = 0, $id = 0)
    {
        $model = new CusOrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getOrderCommodityDetail($user_id, $id));
    }


    public function actionGetPrintData($id){
        $model = new CusOrderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $model->getPrintData($id));
    }

}
