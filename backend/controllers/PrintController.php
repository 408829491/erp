<?php

namespace backend\controllers;

use app\models\form\PrintForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use app\models\PurchaseOffer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class PrintController extends Controller
{


    /**
     * 首页
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PurchaseOffer::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 打印模板管理
     * @return string
     */
    public function actionPrintTemplate(){
        return $this->render('printTemplate');
    }

    /**
     * 打印模板列表
     */
    public function actionTemplateList(){
        $model = new PrintForm();
        return new ApiResponse(200, 'ok', $model->search());
    }


    /**
     * 打印模板编辑
     * @return string
     */
    public function actionUpdate(){
        return $this->render('update');
    }


    /**
     * 打印设备
     * @return string
     */
    public function actionPrintSetting(){
        return $this->render('printSetting');
    }


    /**
     * 保存模板数据
     * @return mixed
     */
    public function actionSaveData(){
        $model = new PrintForm();
        $res = $model->save();
        if (true === $res) {
            return '<script>alert(\'保存成功!\');</script>';
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }


    /**
     * 获取单条模板数据
     * @return mixed
     */
    public function actionGetOne($id){
        $model = new PrintForm();
        return json_encode(['status'=>'success','message'=>'成功','data'=>$model->getOne($id)]);
    }

    /**
     * 获取单条模板数据
     * @return mixed
     */
    public function actionGetTpl($type){
        $model = new PrintForm();
        return json_encode(['status'=>'success','message'=>'成功','data'=>['INCLU_ITEM'=>$model->getTpl($type)]]);
    }

    /**
     * 获取打印配置
     * @return array
     */
    public function getPrinter($type){
        $model = new PrintForm();
        return json_encode($model->getPrintConfig($type));
    }

    /**
     * 获取单条商品分拣数据
     * @param $order_commodity_id
     * @return string
     */
    public function actionPrintData($order_commodity_id){
        $model = new PrintForm();
        return json_encode(['status'=>'1','message'=>'成功','data'=>$model->getPrintPickData($order_commodity_id)]);
    }

    /**
     * 获取拣货汇总数据
     * @param $order_commodity_id
     * @return string
     */
    public function actionPickPrintDataAll($order_commodity_id){
        $model = new PrintForm();
        return json_encode(['status'=>'1','message'=>'成功','data'=>$model->getPrintPickDataAll($order_commodity_id)]);
    }
}
