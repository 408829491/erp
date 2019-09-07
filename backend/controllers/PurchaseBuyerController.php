<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\DeliveryDriver;
use app\models\form\DeliveryDriverForm;
use app\models\form\PurchaseBuyerForm;
use app\models\PurchaseBuyer;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PurchaseBuyerController extends Controller
{


    /**
     * 采购员列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 新增采购员
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 保存采购员信息
     * @param $id
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new PurchaseBuyerForm();
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
        $model = new PurchaseBuyerForm();
        return new ApiResponse(200, 'ok', $model->search());
    }



    /**
     * 采购员编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = PurchaseBuyer::findOne(['id' => $id]);
        return $this->render('update', [
            'model' => $model
        ]);
    }


    /**
     * 司机详情
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
     * 删除销售员
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new PurchaseBuyerForm();
        $res = $model->delete($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }


    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = PurchaseBuyer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}