<?php

namespace app\models\form;

use app\models\CusStoreSlideshow;
use app\models\Model;

class CusStoreSlideshowForm extends Model
{
    public function findPage($pageNum,$pageSize,$filterProperty)
    {
        $pageNum -= 1;
        $query = CusStoreSlideshow::find();

        $storeId = \Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => \Yii::$app->user->identity['store_id']]);
        }

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $searchText = isset($json['searchText'])?$json['searchText']:null;
            if ($searchText != null) {
                $query->andWhere(['like', 'info', "%$searchText%", false]);
            }
        }

        /*$count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;*/
        $tempData = $query->asArray()
            ->orderBy('id DESC')
            ->all();

        $data['list'] = $tempData;
        return $data;
    }
}