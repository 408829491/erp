<?php

namespace api\modules\v1\controllers;

use app\models\form\CommodityFavorForm;
use Yii;
use app\models\CommodityFavor;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommodityFavorController implements the CRUD actions for CommodityFavor model.
 */
class CommodityFavorController extends Controller
{

    /**
     * 获取我的收藏列表
     * @return mixed
     */
    public function actionList()
    {
        $model = new CommodityFavorForm();
        $model->user = $this->user;
        return $model->search();
    }



    /**
     * 更改收藏状态
     * @param $id
     * @return mixed
     */
    public function actionFavor($id)
    {
        $model = new CommodityFavorForm();
        $model->user = $this->user;
        return $model->favor($id);
    }

    public function actionStatus($id){
        $model = new CommodityFavorForm();
        $model->user = $this->user;
        return $model->getFavorStatus($id);
    }
}
