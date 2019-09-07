<?php

namespace backend\controllers;

use app\models\form\PurchaseForm;
use app\models\form\PurchaseRefundForm;
use app\models\Purchase;
use app\models\PurchaseDetail;
use app\models\PurchaseRefund;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * PurchaseController implements the CRUD actions for Purchase model.
 */
class PurchaseRefundController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * 获取采购单列表数据
     */
    public function actionPurchaseList(){
        $model = new PurchaseRefundForm();
        $data = $model->search();
        return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功', $data));
    }


    /**
     * 获取采购单详情数据
     */

    public function actionPurchaseRefundDetail($id){
        $query = PurchaseDetail::find();
        $data['list'] = $query->asArray()
            ->where(['purchase_id'=>$id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功', $data));
    }


    /**
     * 创建采购单
     * @return mixed
     */
    public function actionSave()
    {
        $model = new PurchaseRefundForm();
        $res = $model->save();
        if(true === $res){
            return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功',[]));
        }
        return(new ApiResponse(ApiCode::CODE_ERROR,'失败', $res['data']));
    }

    /**
     * 采购收货
     * @param $purchase_id
     * @return ApiResponse
     */
    public function actionFinish($purchase_id){
        $model = new PurchaseRefundForm();
        if($res = $model->updatePurchaseStatus($purchase_id,3)){
            return new ApiResponse('200','ok');
        }
        else
            return new ApiResponse('0','false',$res);
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionList()
    {
        return $this->render('list');
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
    public function actionView($id){
        $model = $this->getData($id);
        return $this->render('view', $model);
    }


    /**
     * 获取商品详情
     * @param $id
     * @return mixed
     */
    public function getData($id){
        $query = PurchaseRefund::find()
            ->with([
                'details' => function($query) {
                    $query->select('*,commodity_name as name')
                        ->andWhere('is_delete=0');
                },
            ])
            ->where(['id'=>$id])
            ->asArray()
            ->one();
        return $query;
    }

    /**
     * 关闭采购单
     * @param Purchase_id
     * @return ApiResponse
     */
    public function actionClosePurchase($purchase_id){
        $model = new PurchaseRefundForm();
        if($res = $model->updatePurchaseStatus($purchase_id,4)){
            return new ApiResponse('200','ok');
        }
        else
            return new ApiResponse('0','false',$res);
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
