<?php

namespace api\modules\v1\controllers;

use app\models\form\CommodityDemandForm;
use Yii;
use app\models\CommodityDemand;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommodityDemandController implements the CRUD actions for CommodityDemand model.
 */
class CommodityDemandController extends Controller
{

    /**
     * 新品需列表
     * @return array
     */
    public function actionList()
    {
        $model = new CommodityDemandForm();
        $model->user = $this->user;
        return $model->search();
    }

    /**
     * 新品需求保存
     * @return mixed
     */
    public function actionSave()
    {
        $model = new CommodityDemandForm();
        $model->user = $this->user;
        return $model->save();
    }

    /**
     * 根据id查找单个新品需求
     */
    public function actionFindOneById() {
        $model = new CommodityDemandForm();
        $data = $model->findOneById(Yii::$app->request->get('id'));
        return $data;
    }

}
