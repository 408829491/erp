<?php

namespace api\modules\v2\controllers;

use app\models\form\CusCommodityFavorForm;

/**
 * CommodityFavorController implements the CRUD actions for CommodityFavor model.
 */
class CusCommodityFavorController extends Controller
{
    // 查询指定分类的收藏
    public function actionListByFirstTierTypeId()
    {
        $model = new CusCommodityFavorForm();
        $model->user = $this->user;
        return $model->searchByFirstTierTypeId(\Yii::$app->request->post('storeId'), \Yii::$app->request->post('bigTypeId'));
    }

    /**
     * 获取我的收藏列表
     * @return mixed
     */
    public function actionList()
    {
        $model = new CusCommodityFavorForm();
        $model->user = $this->user;
        return $model->search(\Yii::$app->request->post('storeId'));
    }

    /**
     * 更改收藏状态
     * @param $id
     * @return mixed
     */
    public function actionFavor($id, $storeId)
    {
        $model = new CusCommodityFavorForm();
        $model->user = $this->user;
        return $model->favor($id, $storeId);
    }

    public function actionStatus($id){
        $model = new CusCommodityFavorForm();
        $model->user = $this->user;
        return $model->getFavorStatus($id, $this->user->id);
    }
}
