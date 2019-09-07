<?php

namespace backend\controllers;

use app\models\Admin;
use app\models\CusStore;
use app\models\form\CusStoreForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

class CusStoreController extends \yii\web\Controller
{
    /* 管理员查看的所有门店的列表 */
    public function actionIndex()
    {
        /* 刷新缓存*/
       /*$cache = \Yii::$app->cache->flush();*/

        return $this->render('index');
    }

    public function actionIndexData($pageNum=0,$pageSize=10) {
        $query = new CusStoreForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findPage($pageNum, $pageSize, Yii::$app->request->get('filterProperty')));
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    // 保存
    public function actionSave() {

        $model = new CusStoreForm();

        $model1 = new CusStore();

        $model1->attributes = \Yii::$app->request->post();

        $model->saveData($model1, Yii::$app->request->post('username'), Yii::$app->request->post('password'));

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $data = CusStore::findOne($id);
        $adminData = Admin::find()
            ->where(['store_id'=>$id])
            ->one();
        return $this->render('update', ['model'=>$data, 'adminData'=>$adminData]);
    }

    public function actionEdit() {

        $data = CusStore::findOne(Yii::$app->request->post('id'));
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
            CusStore::deleteAll(['id' => $id]);
        }
        return new ApiResponse();
    }

    /**
     * 检查用户名是否可用
     */
    public function actionCheckUsernameIsUsed() {
        $model = Admin::find()->asArray()
            ->where(["username"=>Yii::$app->request->post('username')])
            ->one();
        if ($model == null) {
            return new ApiResponse();
        }
        return new ApiResponse(ApiCode::CODE_SUCCESS, "fail");
    }

}
