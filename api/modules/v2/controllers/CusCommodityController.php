<?php

namespace api\modules\v2\controllers;

use app\models\form\CusCommodityForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;

/**
 * C端商品
 */
class CusCommodityController extends Controller
{
    // 根据id查询单个商品
    public function actionFindOneById() {
        $id = \Yii::$app->request->post('id');
        $detailStatus = \Yii::$app->request->post('detailStatus');
        if ($id == null || $detailStatus == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $model = new CusCommodityForm();
        $data = $model->findOneById($detailStatus, $id, $this->user);
        $data['is_advanced'] = $this->user->is_advanced;
        $data['userMobile'] = $this->user->mobile;

        return $data;
    }

    // 根据关键字搜索商品
    public function actionFindByKeyword() {
        $storeId = \Yii::$app->request->post('storeId');
        $keyword = \Yii::$app->request->post('keyword');
        $pageNum = \Yii::$app->request->post('pageNum');
        $pageSize = \Yii::$app->request->post('pageSize');
        $orderIndex = \Yii::$app->request->post('orderIndex');

        if ($pageNum == null) {
            $pageNum = 1;
        }
        if ($pageSize == null) {
            $pageSize = 10;
        }

        $model = new CusCommodityForm();

        return $model->findPage($storeId, $pageNum, $pageSize, $keyword, $orderIndex, \Yii::$app->request->post('onlySeckill'));
    }
}