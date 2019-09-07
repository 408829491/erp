<?php

namespace app\models\form;

use app\models\CusCommodity;
use app\models\CusCommodityCategory;
use app\models\CusGroup;
use app\models\CusGroupCommodity;
use app\models\CusSeckill;
use app\models\CusSeckillCommodity;
use yii\data\Pagination;
use yii\db\Query;

class CusCommodityCategoryForm extends \yii\base\Model
{
    public function findPage($storeId, $pageNum, $pageSize, $firstTierId, $secondTierId, $orderIndex, $isSelectedSpecialPrice) {
        $pageNum -= 1;

        // 判断是否是特价
        if ($isSelectedSpecialPrice != null && $isSelectedSpecialPrice == 'true') {
            // 加载可用促销商品跟团购商品
            $query1 = CusSeckillCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (1) as type')
                ->where(['exists', CusSeckill::find()
                    ->where("id={{bn_cus_seckill_commodity}}.cus_seckill_id")
                    ->andWhere(['is_close'=>0])
                    ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                    ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])]);
            $query2 = CusGroupCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (2) as type')
                ->where(['exists', CusGroup::find()
                    ->where("id={{bn_cus_group_commodity}}.cus_group_id")
                    ->andWhere(['is_close'=>0])
                    ->andWhere(['date' => date('Y-m-d', time())])]);

            // 加载查询条件
            $query1->andWhere(['store_id'=>$storeId]);
            $query2->andWhere(['store_id'=>$storeId]);
            if ($secondTierId == -1) {
                $query1->andWhere(['like', 'tag', '%热销%', false]);
                $query1->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query2->andWhere(['like', 'tag', '%热销%', false]);
                $query2->andWhere(['type_first_tier_id'=>$firstTierId]);
            } else if ($secondTierId == -2) {
                $query1->andWhere(['like', 'tag', '%当季%', false]);
                $query1->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query2->andWhere(['like', 'tag', '%当季%', false]);
                $query2->andWhere(['type_first_tier_id'=>$firstTierId]);

            } else {
                $query1->andWhere(['type_id'=>$secondTierId]);
                $query2->andWhere(['type_id'=>$secondTierId]);
            }

            $allQuery = (new Query())->from([$query1->union($query2, true)]);
        } else {
            $query = CusCommodity::find()->asArray()
                ->select('bn_cus_commodity.id, bn_cus_commodity.name, bn_cus_commodity_profile.price, bn_cus_commodity_profile.name as unit, bn_cus_commodity.unit as basic_unit, bn_cus_commodity_profile.is_basics_unit, bn_cus_commodity_profile.base_self_ratio, bn_cus_commodity.summary, bn_cus_commodity.sell_num, bn_cus_commodity.pic, bn_cus_commodity.sequence, (0) as type')
                ->leftJoin('bn_cus_commodity_profile', '`bn_cus_commodity_profile`.`commodity_id` = `bn_cus_commodity`.`id` and `bn_cus_commodity_profile`.`is_sell` = 1')
                ->groupBy('bn_cus_commodity_profile.commodity_id');

            // 加载可用促销商品跟团购商品
            $query1 = CusSeckillCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (1) as type')
                ->where(['exists', CusSeckill::find()
                    ->where("id={{bn_cus_seckill_commodity}}.cus_seckill_id")
                    ->andWhere(['is_close'=>0])
                    ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                    ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])]);
            $query2 = CusGroupCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (2) as type')
                ->where(['exists', CusGroup::find()
                    ->where("id={{bn_cus_group_commodity}}.cus_group_id")
                    ->andWhere(['is_close'=>0])
                    ->andWhere(['date' => date('Y-m-d', time())])]);

            // 加载查询条件
            $query->andWhere(['store_id'=>$storeId]);
            $query->andWhere('pic is not null');
            $query->andWhere('is_online = 1');
            $query1->andWhere(['store_id'=>$storeId]);
            $query2->andWhere(['store_id'=>$storeId]);
            if ($secondTierId == -1) {
                $query->andWhere(['like', 'tag', '%热销%', false]);
                $query->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query1->andWhere(['like', 'tag', '%热销%', false]);
                $query1->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query2->andWhere(['like', 'tag', '%热销%', false]);
                $query2->andWhere(['type_first_tier_id'=>$firstTierId]);
            } else if ($secondTierId == -2) {
                $query->andWhere(['like', 'tag', '%当季%', false]);
                $query->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query1->andWhere(['like', 'tag', '%当季%', false]);
                $query1->andWhere(['type_first_tier_id'=>$firstTierId]);
                $query2->andWhere(['like', 'tag', '%当季%', false]);
                $query2->andWhere(['type_first_tier_id'=>$firstTierId]);

            } else {
                $query->andWhere(['type_id'=>$secondTierId]);
                $query1->andWhere(['type_id'=>$secondTierId]);
                $query2->andWhere(['type_id'=>$secondTierId]);
            }

            $allQuery = (new Query())->from([$query->union($query1,true)->union($query2, true)]);
        }

        // 加载排序条件
        $orderData = 'sequence DESC';
        if ($orderIndex == 1) {
            $orderData = 'price DESC';
        } else if ($orderIndex == 2) {
            $orderData = 'price';
        } else if ($orderIndex == 3) {
            $orderData = 'sell_num DESC';
        }

        $count = $allQuery->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $allQuery
            ->orderBy($orderData)
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();

        foreach ($tempData as &$v) {
            if (empty($v['summary'])) {
                $v['summary'] = '时价好物，实惠亲民';
            }
            // 只取第一张图片
            $v['pic']= explode(':;', $v['pic'])[0];
        }

        $data['list'] = $tempData;
        return $data;
    }

    /**
     * 获取格式化分类数据
     * @return array
     */
    public function getCategoryList($storeId){
        $commodityCategory = CusCommodityCategory::find()
            ->select('id,name,pid')
            ->where(['store_id' => $storeId])
            ->asArray()
            ->all();//获取所有分类
        $categoryIndex = array_column($commodityCategory,'name','id'); //生成商分类Map
        $categoryIndexList = array();
        //格式化数组
        foreach($commodityCategory as $k=>$v){
            $categoryIndexList[$v['id']]['id'] = $v['id'];
            $categoryIndexList[$v['id']]['name'] = $v['name'];
            $categoryIndexList[$v['id']]['parent_name'] = ($v['pid'] == 0)?'顶级分类':$categoryIndex[$v['pid']];
        }
        return $categoryIndexList;
    }
}