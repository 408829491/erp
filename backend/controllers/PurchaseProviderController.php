<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\Commodity;
use app\models\DeliveryDriver;
use app\models\form\DeliveryDriverForm;
use app\models\form\PurchaseProviderForm;
use app\models\PurchaseProvider;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PurchaseProviderController extends Controller
{


    /**
     * 供应商列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 新增供应商
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 保存供应商信息
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new PurchaseProviderForm();
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
        $model = new PurchaseProviderForm();
        return new ApiResponse(200, 'ok', $model->search());
    }



    /**
     * 供应商编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = PurchaseProvider::findOne(['id' => $id]);
        return $this->render('update', [
            'model' => $model
        ]);
    }



    /**
     * 删除供应商
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new PurchaseProviderForm();
        $res = $model->delete($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 返回供应商产品
     * @return ApiResponse
     */
    public function actionGetCommodity(){
        $model=new PurchaseProviderForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->getProviderCommodity());
    }

    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = PurchaseProvider::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}