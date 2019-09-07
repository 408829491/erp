<?php

namespace backend\controllers;
use app\models\CusCommodity;
use app\models\CusCommodityCategory;
use app\models\CusStore;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;

/**
 * 门店
 * Class CusCommodityCategoryByStoreManagerController
 * @package backend\controllers
 */
class CusCommodityCategoryByStoreManagerController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData()
    {
        $query = CusCommodityCategory::find();
        // 添加指定门店
        $query->where(['store_id'=>Yii::$app->user->identity['store_id']]);
        $data = $query->select(['id','name','pid','is_show', 'sequence'])->asArray()->all();

        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok", $data);
    }

    // 访问第一层分类
    public function actionFirstTierData() {
        $query = new CusCommodityCategory();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findFirstTierData(['id','name','pid']));
    }

    public function actionCreate($pid)
    {
        return $this->render('create',['pid'=>$pid]);
    }

    public function actionSave(){
        $sequence = Yii::$app->request->post('sequence',0);
        $saveData = new CusCommodityCategory();
        $saveData->attributes = Yii::$app->request->post();
        $saveData->sequence = $sequence == null ? 0 : $sequence;
        $saveData->store_id = Yii::$app->user->identity['store_id'];
        $saveData->is_show = Yii::$app->request->post('is_show') != null ? 1 : 0;
        $saveData->is_create_by_self = 1;

        $saveData->save();

        return new ApiResponse();
    }

    public function actionUpdate($id)
    {
        $model = CusCommodityCategory::findOne($id);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionEdit() {
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $sequence = Yii::$app->request->post('sequence');

        CusCommodityCategory::updateAll(['name' => $name, 'sequence' => $sequence, 'pic_category' => Yii::$app->request->post('pic_category'), 'pic_path_big' => Yii::$app->request->post('pic_path_big'), 'is_show' => Yii::$app->request->post('is_show') != null ? 1 : 0],"id = $id");

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
            // 如果存在商品不能删除
            $data = CusCommodityCategory::findOne($id);
            if ($data->pid == 0) {
                // 第一层id
                $data2 = CusCommodity::find()
                    ->where(['type_first_tier_id' => $id])
                    ->limit(1)
                    ->one();

                if ($data2 != null) {
                    return new ApiResponse(ApiCode::CODE_ERROR, '存在绑定的商品，不能删除');
                }
            } else {
                $data2 = CusCommodity::find()
                    ->where(['type_id' => $id])
                    ->limit(1)
                    ->one();

                if ($data2 != null) {
                    return new ApiResponse(ApiCode::CODE_ERROR, '存在绑定的商品，不能删除');
                }
            }

            CusCommodityCategory::deleteAll(['id' => $id]);
            CusCommodityCategory::deleteAll(['pid' => $id]);
        }
        return new ApiResponse();
    }

    /**
     * 小程序是否启用该分类
     * @param $id
     * @return ApiResponse
     */
    public function actionChangeStatus($id){
        $model = CusCommodityCategory::findOne($id);
        $model->is_show = (isset($model->is_show) && ($model->is_show == 0)) ? 1 : 0;
        if ($model->save()) {
            return new ApiResponse();
        }
        return new ApiResponse('400','failure',$model->getErrors());
    }
}
