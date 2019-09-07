<?php

namespace app\models\form;

use app\models\CusDiscountCoupon;
use app\models\Model;
use Yii;
use yii\data\Pagination;

class CusDiscountCouponForm extends Model
{
    /**
     * 获取分页list数据
     * @param int $pageNum
     * @param int $pageSize
     * @param $filterProperty
     * @param $select
     * @return mixed
     */
    public function findPage($pageNum,$pageSize,$filterProperty)
    {
        $pageNum -= 1;
        $query = CusDiscountCoupon::find();

        // 添加筛选条件
        $storeId = Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => Yii::$app->user->identity['store_id']]);
        }

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $type = isset($json['type'])?$json['type']:null;
            if ($type != null) {
                $query->andWhere(['type' => $type]);
            }

            $searchText = isset($json['searchText'])?$json['searchText']:null;
            if ($searchText != null) {
                $query->andWhere(['like', 'name', "%$searchText%", false]);
            }
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        $data['list'] = $tempData;
        return $data;
    }

    /**
     * 获取分页list数据
     * @param int $pageNum
     * @param int $pageSize
     * @param $filterProperty
     * @param $select
     * @return mixed
     */
    public function findPage2($pageNum,$pageSize,$filterProperty)
    {
        $pageNum -= 1;
        $query = CusDiscountCoupon::find();

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $type = isset($json['type'])?$json['type']:null;
            if ($type != null) {
                $query->andWhere(['type' => $type]);
            }

            $searchText = isset($json['searchText'])?$json['searchText']:null;
            if ($searchText != null) {
                $query->andWhere(['like', 'name', "%$searchText%", false]);
            }
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        $data['list'] = $tempData;
        return $data;
    }
}
