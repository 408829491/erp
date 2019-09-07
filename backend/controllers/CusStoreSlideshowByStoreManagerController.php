<?php

namespace backend\controllers;

use app\models\CusStoreSlideshow;
use app\models\form\CusStoreSlideshowForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

/**
 * 门店管理轮播图
 * Class CusStoreByStoreManagerController
 * @package backend\controllers
 */

class CusStoreSlideshowByStoreManagerController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum = 0, $pageSize = 10)
    {
        $query = new CusStoreSlideshowForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, "ok", $query->findPage($pageNum, $pageSize, Yii::$app->request->get('filterProperty')));
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    // 保存
    public function actionSave() {

        $model = new CusStoreSlideshow();
        $model->attributes = Yii::$app->request->post();
        $model->store_id = Yii::$app->user->identity['store_id'];
        $model->save();

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $data = CusStoreSlideshow::findOne($id);

        return $this->render('update', ['model'=>$data]);
    }

    public function actionEdit() {

        $data = CusStoreSlideshow::findOne(Yii::$app->request->post('id'));
        $data->attributes = Yii::$app->request->post();
        if (!$data->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败', $data->errors);
        }
        $data->save();

        return new ApiResponse();
    }

    public function actionDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids == null) {
            return new ApiResponse(ApiCode::CODE_ERROR,"失败");
        }
        $ids2 = explode(",",$ids);
        foreach ( $ids2 as $id) {
            CusStoreSlideshow::deleteAll(['id' => $id]);
        }
        return new ApiResponse();
    }
}
