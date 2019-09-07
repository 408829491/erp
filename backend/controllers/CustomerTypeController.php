<?php

namespace backend\controllers;

use app\models\DeliveryTime;
use app\models\form\CustomerTypeForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use app\models\CustomerType;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CustomerTypeController implements the CRUD actions for CustomerType model.
 */
class CustomerTypeController extends Controller
{

    /**
     * 类型列表
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 列表数据
     */
    public function actionGetIndexData()
    {
        $model = new CustomerTypeForm();
        return new ApiResponse(200, 'ok', $model->search());
    }


    public function actionCreate()
    {
        $model = new CustomerType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }

        return $this->render('create', [
            'model' => $model, 'delivery_time' => DeliveryTime::find()->all()
        ]);
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model, 'delivery_time' => DeliveryTime::find()->all()
        ]);
    }

    public function actionSave($id = 0)
    {
        if($id){
            $model = $this->findModel($id);
        }else{
            $model = new CustomerType();
            $model->create_time=time();
        }
        $model->attributes=Yii::$app->request->post();
        if ($model->save()) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $model->getErrors());
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = CustomerType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
