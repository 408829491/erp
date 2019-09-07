<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\CommodityProfile;
use app\models\Seckill;
use app\models\SeckillCommodity;
use yii\data\Pagination;
use yii\db\Query;

class CommodityForm extends \yii\base\Model
{
    // 获取新品推荐（展示16个）
    public function findNewCommodity($user) {
        $data = Commodity::find()
            ->select('id,name,price,unit,pic')
            ->where(['is_online'=>1])
            ->orderBy('id DESC')
            ->limit(16)
            ->asArray()
            ->all();
        foreach ($data as $value=>&$item) {
            $item['pic'] = explode(':;', $item['pic'])[0];

            $leftJoinOn = 'bn_commodity_profile_detail.commodity_profile_id = bn_commodity_profile.id and bn_commodity_profile_detail.type_id = '.$user->c_type_id;
            // 获取子表可售卖的单位
            $data2 = CommodityProfile::find()
                ->select('bn_commodity_profile.id, bn_commodity_profile.commodity_id, bn_commodity_profile.name, bn_commodity_profile.is_basics_unit, bn_commodity_profile.base_self_ratio, bn_commodity_profile_detail.price')
                ->leftJoin('bn_commodity_profile_detail', $leftJoinOn)
                ->where(['bn_commodity_profile.is_sell' => 1, 'bn_commodity_profile.commodity_id' => $item['id']])
                ->one();

            $item['unitList'] = $data2;
        }
        return $data;
    }

    // 根据Id获取单个商品
    public function findOneById($id) {
        $model = Commodity::find()->asArray()
            ->select('id,pic,name,summary,brand,price,unit,summary,type_id')
            ->where(['id'=>$id])
            ->one();
        return $model;
    }

    // 获取分类的分页List数据（联合商品表跟促销商品表）
    public function findPage($pageNum=1, $pageSize=10, $type_id, $isSeckill, $order, $name, $user)
    {
        $pageNum -= 1;

        // 查询促销商品 进行中的活动商品
        $query1 = SeckillCommodity::find()
            ->select('id,name,pic,unit,price as old_price,activity_price as price,is_seckill,sequence,sell_num,commodity_id')
            ->where(['exists', Seckill::find()
                ->where("id={{bn_seckill_commodity}}.seckill_id")
                ->andWhere(['is_close'=>0])
                ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])]);

        $allQuery = null;
        // 判断是否只是促销商品
        if ($isSeckill == null) {
            $query = Commodity::find()
                ->select('id,name,pic,unit,activity_price as old_price,price,is_seckill,sequence,sell_num,id as commodity_id')
                ->where(['is_online'=>1]);

            // 查询条件
            if ($type_id != null) {
                $query->andWhere(['type_id' => $type_id]);
                $query1->andWhere(['type_id' => $type_id]);
            }
            if ($name != null) {
                $query->andWhere(['like', 'name', "%$name%", false]);
                $query1->andWhere(['like', 'name', "%$name%", false]);
            }

            // 联合
            $allQuery = (new Query())->from([$query1->union($query,true)]);
        } else {
            // 查询条件
            if ($type_id != null) {
                $query1->andWhere(['type_id' => $type_id]);
            }
            if ($name != null) {
                $query1->andWhere(['like', 'name', "%$name%", false]);
            }

            $allQuery = (new Query())->from($query1);
        }

        // 排序条件
        $orderData = 'sequence DESC';
        if ($order != null) {
            $orderData = $order;
        }

        $count = $allQuery->count();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $allQuery
            ->orderBy($orderData)
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();

        // 只展示第一张主图
        foreach ($tempData as $value=>&$item) {
            $item['pic'] = explode(':;', $item['pic'])[0];

            // 如果是普通商品加载单位价格
            if ($item['is_seckill'] == 0) {
                $leftJoinOn = 'bn_commodity_profile_detail.commodity_profile_id = bn_commodity_profile.id and bn_commodity_profile_detail.type_id = '.$user->c_type_id;
                // 获取子表可售卖的单位
                $data2 = CommodityProfile::find()
                    ->select('bn_commodity_profile.id, bn_commodity_profile.commodity_id, bn_commodity_profile.name, bn_commodity_profile.is_basics_unit, bn_commodity_profile.base_self_ratio, bn_commodity_profile_detail.price')
                    ->leftJoin('bn_commodity_profile_detail', $leftJoinOn)
                    ->where(['bn_commodity_profile.is_sell' => 1, 'bn_commodity_profile.commodity_id' => $item['id']])
                    ->one();

                $item['unitList'] = $data2;
            }
        }

        $data['list'] = $tempData;
        return $data;
    }

    // 猜你喜欢
    public function findListByRandom($type_id, $user, $id)
    {
        $data = Commodity::find()->asArray()
            ->where(['type_id'=>$type_id])
            ->andWhere(['!=', 'id', $id])
            ->limit(8)
            ->all();

        // 处理图片
        foreach ($data as $value=>&$item) {
            $item['pic'] = explode(':;', $item['pic'])[0];

            $leftJoinOn = 'bn_commodity_profile_detail.commodity_profile_id = bn_commodity_profile.id and bn_commodity_profile_detail.type_id = '.$user->c_type_id;
            // 获取子表可售卖的单位
            $data2 = CommodityProfile::find()
                ->select('bn_commodity_profile.id, bn_commodity_profile.commodity_id, bn_commodity_profile.name, bn_commodity_profile.is_basics_unit, bn_commodity_profile.base_self_ratio, bn_commodity_profile_detail.price')
                ->leftJoin('bn_commodity_profile_detail', $leftJoinOn)
                ->where(['bn_commodity_profile.is_sell' => 1, 'bn_commodity_profile.commodity_id' => $item['id']])
                ->one();

            $item['unitList'] = $data2;
        }
        return $data;
    }

    // 校对商品并且添加分类字段
    public function checkCommodity($shopCartList, $user)
    {
        $data = json_decode($shopCartList, true);
        $classData = $this->getCategoryList();
        foreach ($data as $index=>&$item) {
            if ($item['is_seckill'] == 1) {
                // 促销商品, 不存在删除记录
                $data1 = SeckillCommodity::findOne($item['id']);

                if ($data1 == null) {
                    array_splice($data, $index, 1);
                } else {
                    // 查询是否过期
                    $data2 = Seckill::find()
                        ->where(['id'=>$data1->seckill_id])
                        ->andWhere(['is_close'=>0])
                        ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                        ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])
                        ->one();

                    if ($data2 == null) {
                        // 过期
                        $item['is_over'] = true;
                    }
                    $item['type_first_tier_id'] = $data1->type_first_tier_id;
                    $item['type_id'] = $data1->type_id;
                    $item['parent_type_name'] = $classData[$item['type_id']]['parent_name'];
                    $item['type_name'] = $classData[$item['type_id']]['name'];
                }
            } else {
                // 普通商品根据单位更新价格， 不存在删除记录
                $data1 = Commodity::findOne($item['id']);

                if ($data1 == null) {
                    array_splice($data, $index, 1);
                } else {
                    // 根据单位更新字段
                    $data2 = CommodityProfile::find()->asArray()
                        ->select('bn_commodity_profile.id, bn_commodity_profile.commodity_id, bn_commodity_profile.name, bn_commodity_profile.is_basics_unit, bn_commodity_profile.base_self_ratio, bn_commodity_profile_detail.price')
                        ->leftJoin('bn_commodity_profile_detail', 'bn_commodity_profile_detail.commodity_profile_id = bn_commodity_profile.id and bn_commodity_profile_detail.type_id = '.$user->c_type_id)
                        ->where(['bn_commodity_profile.commodity_id' => $item['id'],'bn_commodity_profile.name' => $item['unit'], 'bn_commodity_profile.base_self_ratio' => $item['base_self_ratio']])
                        ->one();
                    if ($data2 == null) {
                        array_splice($data, $index, 1);
                    } else {
                        $item['price'] = $data2['price'];
                        $item['is_basics_unit'] = $data2['is_basics_unit'];
                        $item['base_self_ratio'] = $data2['base_self_ratio'];
                        $item['base_unit'] = $data1['unit'];
                        $item['type_first_tier_id'] = $data1->type_first_tier_id;
                        $item['type_id'] = $data1->type_id;
                        $item['parent_type_name'] = $classData[$item['type_id']]['parent_name'];
                        $item['type_name'] = $classData[$item['type_id']]['name'];
                        $item['provider_id'] = $data1->provider_id;
                        $item['provider_name'] = $data1->provider_name;
                        $item['agent_id'] = $data1->agent_id;
                        $item['agent_name'] = $data1->agent_name;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 获取格式化分类数据
     * @return array
     */
    public function getCategoryList(){
        $commodityCategory = CommodityCategory::find()
            ->select('id,name,pid')
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