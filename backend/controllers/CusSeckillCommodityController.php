<?php

namespace backend\controllers;

use app\models\CusSeckill;
use app\models\CusSeckillCommodity;
use app\models\form\CusSeckillForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

/**
 * C端秒杀
 * Class CusSeckillCommodityController
 * @package backend\controllers
 */
class CusSeckillCommodityController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum,$pageSize) {
        $query = new CusSeckillForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findPage($pageNum,$pageSize,Yii::$app->request->get('filterProperty'),null));
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionSave() {
        $model = new CusSeckill();
        $model->attributes = Yii::$app->request->post();
        $model->store_id = 3;
        $model1 = new CusSeckillForm();
        $model1->saveData($model, Yii::$app->request->post('subList'));

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $subList = CusSeckillCommodity::find()->where(['cus_seckill_id' => $id])->select('cus_commodity_id as id,is_online,name,pic,unit,price,alias,channel_type,notice,type_id,type_first_tier_id,activity_price,limit_buy')->asArray()->all();
        return $this->render('update', [
            'model' => CusSeckill::findOne($id),
            'subList' => json_encode($subList)
        ]);
    }

    public function actionEdit() {
        $model = CusSeckill::findOne(Yii::$app->request->post('id'));
        $model->attributes = Yii::$app->request->post();
        $model1 = new CusSeckillForm();
        $model1->editData($model, Yii::$app->request->post('subList'), Yii::$app->request->post('id'));

        return new ApiResponse();
    }

    public function actionDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids == null) {
            return new ApiResponse(ApiCode::CODE_ERROR,"失败");
        }
        CusSeckill::deleteAll(['id'=>$ids]);
        return new ApiResponse();
    }

    // 关闭活动
    public function actionClose() {
        $model = CusSeckill::findOne(Yii::$app->request->post('id'));
        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败');
        }
        // 判断活动是否结束
        if ($model->is_close == 1 || strtotime($model->end_time) < strtotime('now')) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'Ok', ['code' => '0']);
        }
        $model->is_close = 1;
        $model->close_time = date('Y-m-d H:i:s');
        $model->closer_id = Yii::$app->user->identity->id;
        $model->close_name = Yii::$app->user->identity->nickname;

        $model->save();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'Ok', ['code' => '1']);
    }
}
