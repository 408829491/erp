<?php

namespace backend\controllers;

use app\models\CusStore;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

class CusStoreByStoreManagerController extends \yii\web\Controller
{
    /* 各个门店的查看的单独的信息 */
    public function actionFindOneByStore() {
        $data = CusStore::findOne(Yii::$app->user->identity['store_id']);

        return $this->render('update', ['model' => $data, 'adminData' => Yii::$app->user->identity]);
    }

    public function actionEdit() {

        $data = CusStore::findOne(\Yii::$app->user->identity['store_id']);
        $data->attributes = Yii::$app->request->post();
        if (!$data->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败', $data->errors);
        }
        $data->save();

        return new ApiResponse();
    }

}
