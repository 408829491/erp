<?php

namespace api\modules\v2\controllers;

use app\models\CusCommodityCategory;
use app\models\form\CusCommodityCategoryForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;

/**
 * C端商品分类
 */
class CusCommodityCategoryController extends Controller
{
    // 根据店铺id加载大类
    public function actionFindBigTypeByStoreId() {
        $id = \Yii::$app->request->post('storeId');
        $data = CusCommodityCategory::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['pid'=>0])
            ->andWhere(['is_show' => 1])
            ->orderBy('sequence DESC')
            ->all();

        return $data;
    }

    // 根据小类id加载商品
    public function actionFindBySecondTierId() {
        $storeId = \Yii::$app->request->post('storeId');
        $firstTierId = \Yii::$app->request->post('firstTierId');
        $secondTierId = \Yii::$app->request->post('secondTierId');
        $pageNum = \Yii::$app->request->post('pageNum');
        $pageSize = \Yii::$app->request->post('pageSize');
        $orderIndex = \Yii::$app->request->post('orderIndex');
        if ($pageNum == null) {
            $pageNum = 1;
        }
        if ($pageSize == null) {
            $pageSize = 10;
        }

        $model = new CusCommodityCategoryForm();

        return $model->findPage($storeId, $pageNum, $pageSize, $firstTierId, $secondTierId, $orderIndex, \Yii::$app->request->post('isSelectedSpecialPrice'));
    }

    // 加载大类，以及所属小类和大类相关热卖商品
    public function actionFindByFirstTierId() {
        // 查找大类id
        $storeId = \Yii::$app->request->post('storeId');
        $firstTierId = \Yii::$app->request->post('firstTierId');
        $data = CusCommodityCategory::find()->asArray()
            ->where([
                'pid'=>0,
                'store_id'=>$storeId
            ])
            ->andWhere(['!=','id',$firstTierId])
            ->andWhere(['is_show' => 1])
            ->all();

        // 查找对应的小类
        if ($firstTierId == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $data2 = CusCommodityCategory::find()->asArray()
            ->where(['pid'=>$firstTierId,
            'store_id'=>$storeId
            ])
            ->andWhere(['is_show' => 1])
            ->all();

        // 查询商品数据
        $pageNum = \Yii::$app->request->post('pageNum');
        $pageSize = \Yii::$app->request->post('pageSize');
        $orderIndex = \Yii::$app->request->post('orderIndex');
        if ($pageNum == null) {
            $pageNum = 1;
        }
        if ($pageSize == null) {
            $pageSize = 10;
        }
        $model = new CusCommodityCategoryForm();

        return ['firstTierData'=>$data, 'secondTierData'=>$data2, 'dataList'=>$model->findPage($storeId, $pageNum, $pageSize, $firstTierId, -1, $orderIndex, \Yii::$app->request->post('isSelectedSpecialPrice'))];
    }

}