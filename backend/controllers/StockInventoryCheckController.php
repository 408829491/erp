<?php

namespace backend\controllers;

use app\models\form\StockInForm;
use app\models\form\StockInventoryCheckForm;
use app\models\Purchase;
use app\models\StockIn;
use app\models\StockInDetail;
use app\models\StockInventoryCheck;
use app\models\StockInventoryCheckDetail;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class StockInventoryCheckController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * 获取入库单列表数据
     */
    public function actionList(){
        $model = new StockInventoryCheckForm();
        $data = $model->search();
        return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功', $data));
    }


    /**
     * 获取入库单详情数据
     */

    public function actionDetail($id){
        $query = StockInventoryCheckDetail::find();
        $data['list'] = $query->asArray()
            ->where(['check_id'=>$id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功', $data));
    }


    /**
     * 创建入库单
     * @return ApiResponse
     * @throws \yii\db\Exception
     */
    public function actionSave()
    {
        $model = new StockInventoryCheckForm();
        $res = $model->save();
        if(true === $res){
            return(new ApiResponse(ApiCode::CODE_SUCCESS,'成功',[]));
        }
        return(new ApiResponse(ApiCode::CODE_ERROR,'失败', $res['data']));
    }

    /**
     * 单据审核
     * @param $id
     * @return ApiResponse
     * @throws \yii\base\ErrorException
     */
    public function actionCheck($id){
        $model = new StockInventoryCheckForm();
        if($res = $model->updateStatus($id,1)){
            return new ApiResponse('200','ok');
        }
        else
            return new ApiResponse('0','false',$res);
    }

    /**
     * 创建单据
     * @return string
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 编辑页面
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = $this->getData($id);
        return $this->render('update', $model);
    }


    /**
     * 单据详情
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
        $query =StockInventoryCheck::find()
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
     * 关闭单据
     * @param Purchase_id
     * @return ApiResponse
     */
    public function actionClosePurchase($id){
        $model = new StockInventoryCheckForm();
        if($res = $model->updateStatus($id,3)){
            return new ApiResponse('200','ok');
        }
        else
            return new ApiResponse('0','false',$res);
    }


    /**
     * 获取单据打印数据
     * @return mixed
     */
    public function actionGetPrintData($id){
        $model = new StockInventoryCheckForm();
        return new ApiResponse('200','ok', $model->getPrintData($id));
    }


}
