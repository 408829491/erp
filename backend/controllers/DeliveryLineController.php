<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\DeliveryDriver;
use app\models\DeliveryLine;
use app\models\form\DeliveryLineForm;
use app\models\form\GpsForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DeliveryLineController extends Controller
{


    /**
     * 线路列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 配送地图
     * @return mixed
     */
    public function actionMap()
    {
        return $this->render('map');
    }

    /**
     * 新增线路
     * @return mixed
     */
    public function actionCreate()
    {
        $model = DeliveryDriver::find()->where(['type'=>'2'])->all();
        return $this->render('create', ['model' => $model]);
    }

    /**
     * 保存线路信息
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new DeliveryLineForm();
        $res = $model->save($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 列表数据
     */
    public function actionGetIndexData()
    {
        $model = new DeliveryLineForm();
        return new ApiResponse(200, 'ok', $model->search());
    }


    /**
     * 线路编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = DeliveryLine::findOne(['id' => $id]);
        $driver = DeliveryDriver::find()->where(['type'=>2])->all();
        return $this->render('update', [
            'model' => $model,
            'driver' => $driver
        ]);
    }


    /**
     * 线路详情
     * @param $id
     * @return string
     */
    public function actionView($code)
    {
        return $this->render('view', [
            'code' => $code
        ]);
    }

    /**
     * 删除线路
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new DeliveryLineForm();
        $res = $model->delete($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }


    public function actionGetLineList()
    {
        $model = new DeliveryLineForm();
        return new ApiResponse(ApiCode::CODE_ERROR, 'false',$model->getLineList());
    }


    public function actionTaskByOrder(){
        return $this->render('taskByOrder');
    }

    public function actionTaskByCustomer(){
        return $this->render('taskByCustomer');
    }

    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = DeliveryLine::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}