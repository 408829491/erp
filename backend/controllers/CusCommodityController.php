<?php

namespace backend\controllers;

use app\models\Config;
use app\models\CusCommodity;
use app\models\CusCommodityProfile;
use app\models\CusStore;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

class CusCommodityController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix" => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
                    "imageRoot" => Yii::getAlias("@webroot"),
                ],
            ]
        ];
    }

    /* 管理员 */
    public function actionIndex()
    {
        return $this->render('index', ['storeId' => Yii::$app->request->get('storeId')]);
    }

    public function actionIndexData($pageNum = 0, $pageSize = 10)
    {
        $query = new CusCommodity();
        return new ApiResponse(ApiCode::CODE_SUCCESS, "ok", $query->findPage($pageNum, $pageSize, Yii::$app->request->get('filterProperty'), null));
    }

    public function actionCreate()
    {
        return $this->render('create', ['storeId' => Yii::$app->request->get('storeId')]);
    }

    // 保存
    public function actionSave()
    {

        $model = new CusCommodity();

        $model->attributes = Yii::$app->request->post();

        $model->is_online = Yii::$app->request->post('is_online') != null ? 1 : 0;
        $model->is_time_price = Yii::$app->request->post('is_time_price') != null ? 1 : 0;
        $storeId = Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $model->store_id = $storeId;
        } else {
            $model->store_id = Yii::$app->user->identity['store_id'];
        }

        if (!$model->validate()) {
            new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors);
        };
        $model->saveData($model, Yii::$app->request->post('unitList'));

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $modelSubBasicUnit = CusCommodityProfile::find()->where(['is_basics_unit' => 1, 'commodity_id' => $id])->one();
        $unitModel = Config::findOne("25");
        $modelSubUnitLList = CusCommodityProfile::find()->where(['is_basics_unit' => 0, 'commodity_id' => $id])->all();
        return $this->render('update', ['storeId' => Yii::$app->request->get('storeId'), 'id' => $id, 'model' => CusCommodity::findOne($id), 'modelSubBasicUnit' => $modelSubBasicUnit, 'modelSubUnitLList' => $modelSubUnitLList, 'unitList' => explode(":;", $unitModel->value)]);
    }

    // 修改保存
    public function actionEdit()
    {

        $model = CusCommodity::findOne(Yii::$app->request->post('id'));
        $model->attributes = Yii::$app->request->post();

        $model->is_online = Yii::$app->request->post('is_online') != null ? 1 : 0;
        $model->is_time_price = Yii::$app->request->post('is_time_price') != null ? 1 : 0;

        if (!$model->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors);
        }
        $model->edit($model, Yii::$app->request->post('unitList'));

        return new ApiResponse();
    }

    // 修改上下架
    public function actionUpdateIsOnline()
    {
        $is_online = Yii::$app->request->post('is_online') == 'true' ? 1 : 0;
        CusCommodity::updateAll(['is_online' => $is_online], ['id' => Yii::$app->request->post('id')]);
        return new ApiResponse();
    }

    public function actionDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, "失败");
        }
        $ids2 = explode(",", $ids);
        foreach ($ids2 as $id) {
            CusCommodity::deleteAll(['id' => $id]);
        }
        return new ApiResponse();
    }

    // 选择商品的界面
    public function actionList()
    {
        return $this->render('list', []);
    }

    // 查询商品的所有单位
    public function actionFindCommodityUnitDataList($pageNum = 0, $pageSize = 10) {
        $query = new CusCommodity();
        return new ApiResponse(ApiCode::CODE_SUCCESS, "ok", $query->findCommodityUnitDataList($pageNum, $pageSize, Yii::$app->request->get('filterProperty'), null));
    }
}
