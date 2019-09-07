<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\form\CusDeliveryDriverForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use common\models\UserDelivery;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CusDeliveryDriverController extends Controller
{


    /**
     * 配送员列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', ['storeId' => Yii::$app->request->get('storeId')]);
    }

    /**
     * 新增配送员
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create', ['storeId' => Yii::$app->request->get('storeId')]);
    }

    /**
     * 保存配送员信息
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new CusDeliveryDriverForm();
        $res = $model->save($id, Yii::$app->request->get('storeId'));
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 配送员列表数据
     */
    public function actionGetIndexData()
    {
        $model = new CusDeliveryDriverForm();
        return new ApiResponse(200, 'ok', $model->search(\Yii::$app->request->get('storeId')));
    }



    /**
     * 配送员编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = UserDelivery::findOne(['id' => $id]);
        return $this->render('update', [
            'model' => $model
        ]);
    }


    /**
     * 配送员详情
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
     * 删除配送员
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new CusDeliveryDriverForm();
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
        if (($model = CusDeliveryDriver::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}