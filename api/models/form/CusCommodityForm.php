<?php

namespace app\models\form;

use api\modules\v2\controllers\CusCommodityFavorController;
use app\models\CusComment;
use app\models\CusCommodity;
use app\models\CusCommodityProfile;
use app\models\CusDiscountCoupon;
use app\models\CusDiscountCouponGetRecord;
use app\models\CusGroup;
use app\models\CusGroupCommodity;
use app\models\CusGroupOrder;
use app\models\CusGroupOrderDetail;
use app\models\CusOrder;
use app\models\CusSeckill;
use app\models\CusSeckillCommodity;
use app\utils\ReplaceUtils;
use Exception;
use yii\data\Pagination;
use yii\db\Query;

class CusCommodityForm extends \yii\base\Model
{
    public function findPage($storeId, $pageNum, $pageSize, $keyword, $orderIndex, $onlySeckill)
    {
        $pageNum -= 1;

        // 判断是否是特价
        if ($onlySeckill != null && $onlySeckill == 'true') {
            // 加载可用促销商品跟团购商品
            $query1 = CusSeckillCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (1) as type')
                ->where(['exists', CusSeckill::find()
                    ->where("id={{bn_cus_seckill_commodity}}.cus_seckill_id")
                    ->andWhere(['is_close' => 0])
                    ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                    ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])]);
            $query2 = CusGroupCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (2) as type')
                ->where(['exists', CusGroup::find()
                    ->where("id={{bn_cus_group_commodity}}.cus_group_id")
                    ->andWhere(['is_close' => 0])
                    ->andWhere(['date' => date('Y-m-d', time())])]);

            // 加载查询条件
            $query1->andWhere(['store_id' => $storeId]);
            $query1->andWhere(['like', 'name', "%$keyword%", false]);
            $query2->andWhere(['store_id' => $storeId]);
            $query2->andWhere(['like', 'name', "%$keyword%", false]);

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
                    ->andWhere(['is_close' => 0])
                    ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s', time())])
                    ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s', time())])]);
            $query2 = CusGroupCommodity::find()->asArray()
                ->select('id, name, activity_price as price, unit, unit as basic_unit,(1) as is_basics_unit, (0) as base_self_ratio, summary, sell_num, pic, sequence, (2) as type')
                ->where(['exists', CusGroup::find()
                    ->where("id={{bn_cus_group_commodity}}.cus_group_id")
                    ->andWhere(['is_close' => 0])
                    ->andWhere(['date' => date('Y-m-d', time())])]);

            // 加载查询条件
            $query->andWhere(['store_id' => $storeId]);
            $query->andWhere('pic is not null');
            $query->andWhere('is_online = 1');
            $query->andWhere(['like', 'bn_cus_commodity.name', "%$keyword%", false]);
            $query1->andWhere(['store_id' => $storeId]);
            $query1->andWhere(['like', 'name', "%$keyword%", false]);
            $query2->andWhere(['store_id' => $storeId]);
            $query2->andWhere(['like', 'name', "%$keyword%", false]);

            $allQuery = (new Query())->from([$query->union($query1, true)->union($query2, true)]);
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
            $v['pic'] = explode(':;', $v['pic'])[0];
        }

        $data['list'] = $tempData;
        return $data;
    }

    // 根据id查询单个商品,status为0普通商品1促销2拼团
    public function findOneById($detailStatus, $id, $user)
    {
        if ($detailStatus == 0) {
            $data = CusCommodity::findOne($id)->toArray();
            // 加载可用的单位（规格）
            $unitData = CusCommodityProfile::find()->asArray()
                ->where(['is_sell' => 1])
                ->andWhere(['commodity_id' => $data['id']])
                ->all();

            $commodityId = $id;
            $usableGroup = [];
        } else if ($detailStatus == 1) {
            $data = CusSeckillCommodity::findOne($id)->toArray();
            $unitData = [['name' => $data['unit'], 'price' => $data['activity_price'], 'is_basics_unit' => 1]];

            // 查询促销表
            $seckillData = CusSeckill::findOne($data['cus_seckill_id'])->toArray();
            $data['is_limit_buy_num'] = $seckillData['is_limit_buy_num'];

            $commodityId = $data['cus_commodity_id'];
            $usableGroup = [];
        } else if ($detailStatus == 2) {
            $data = CusGroupCommodity::findOne($id)->toArray();
            $data['face_url'] = $user->head_pic;
            $unitData = [['name' => $data['unit'], 'price' => $data['activity_price'], 'is_basics_unit' => 1]];

            // 查询团购表
            $groupData = CusGroup::findOne($data['cus_group_id'])->toArray();
            $data['is_limit_buy_num'] = $groupData['is_limit_buy_num'];

            // 查询分类数据获取名称
            $classQuery = new CusCommodityCategoryForm();
            $classData = $classQuery->getCategoryList($data['store_id']);

            $data['parent_type_name'] = $classData[$data['type_id']]['parent_name'];
            $data['type_name'] = $classData[$data['type_id']]['name'];

            $commodityId = $data['cus_commodity_id'];

            // 加载可参加的团购
            $usableGroup = CusGroupOrder::find()->asArray()
                ->select('bn_cus_group_order.*,bn_cus_member.head_pic,bn_cus_member.nickname')
                ->leftJoin('bn_cus_member', 'bn_cus_member.id=bn_cus_group_order.user_id')
                ->where(['bn_cus_group_order.group_commodity_id' => \Yii::$app->request->post('groupCommodityId')])
                ->andWhere(['bn_cus_group_order.is_group_commander' => 1])
                ->andWhere(['bn_cus_group_order.is_group_success' => 0])
                ->andWhere(['!=', 'bn_cus_group_order.user_id', $user->id])
                ->andWhere(['not exists', CusGroupOrder::find()->alias('t')
                    ->where("t.group_id={{bn_cus_group_order}}.group_id")
                    ->andWhere(['t.user_id' => $user->id])
                ])
                ->all();
        }

        // 加载是否收藏
        $cusCommodityFavorForm = new CusCommodityFavorForm();
        $cusCommodityFavorForm->user = $user;
        $isFavor = $cusCommodityFavorForm->getFavorStatus($commodityId, $user->id);

        // 加载最新的有评价内容的评价
        $commentData = CusComment::find()->asArray()
            ->select('bn_cus_comment.id, bn_cus_comment.rank, bn_cus_comment.content, bn_cus_comment.create_time, bn_cus_member.nickname, bn_cus_member.head_pic')
            ->leftJoin('bn_cus_member', 'bn_cus_member.id=bn_cus_comment.user_id')
            ->where(['commodity_id' => $commodityId])
            ->andWhere('content is not null')
            ->orderBy('id DESC')
            ->one();
        if ($commentData != null) {
            $commentData['showDateTime'] = date('Y.m.d', $commentData['create_time']);
            $commentData['nickname'] = ReplaceUtils::replace($commentData['nickname']);
        }

        if (empty($data['summary'])) {
            $data['summary'] = '时价好物，实惠亲民';
        }

        // 加载可领取优惠券
        $discountData = CusDiscountCoupon::find()->asArray()
            ->where(['store_id' => $data['store_id']])
            ->andWhere(['<=', 'start_date', date('Y-m-d')])
            ->andWhere(['>', 'end_date', date('Y-m-d')])
            ->all();
        foreach ($discountData as &$item) {
            $cusDiscountCouponGetRecord = CusDiscountCouponGetRecord::find()
                ->where(['cus_member_id' => $user->id, 'discount_coupon_id' => $item])
                ->one();

            if ($cusDiscountCouponGetRecord == null) {
                $item['isGet'] = false;
            } else {
                $item['isGet'] = true;
            }
        }

        // 查询猜你喜欢根据小类推荐
        $likeData = CusCommodity::find()->asArray()
            ->select('id, name, price, unit, summary, sell_num, pic, sequence')
            ->where(['type_id' => $data['type_id']])
            ->andWhere(['not in', 'id', $data['id']])
            ->andWhere('pic is not null')
            ->limit(24)
            ->all();

        foreach ($likeData as &$v) {
            if (empty($v['summary'])) {
                $v['summary'] = '时价好物，实惠亲民';
            }
            // 只取第一张图片
            $v['pic'] = explode(':;', $v['pic'])[0];
            // 查询可售卖的单位
            $cusCommodityProfile = CusCommodityProfile::find()->asArray()
                ->where(['commodity_id' => $v['id'], 'is_sell' => 1])
                ->one();
            $v['basic_unit'] = $v['unit'];
            $v['unit'] = $cusCommodityProfile['name'];
            $v['is_basics_unit'] = $cusCommodityProfile['is_basics_unit'];
            $v['price'] = $cusCommodityProfile['price'];
            $v['base_self_ratio'] = $cusCommodityProfile['base_self_ratio'];
        }
        return ['dataOne' => $data, 'likeData' => $likeData, 'discountData' => $discountData, 'unitData' => $unitData, 'isFavor' => $isFavor, 'commentData' => $commentData, 'usableGroup' => $usableGroup];
    }

    // 保存商品
    public function saveData($model, $unitLit)
    {
        $transaction = CusCommodity::getDb()->beginTransaction();

        try {

            if (!$model->validate()) {
                throw new \yii\base\Exception();
            };
            $newRecord = $model->isNewRecord;
            $model->save();
            // 保存子表
            $this->saveSubUnitList($model, $unitLit, $newRecord);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 根据json保存子表
    public function saveSubUnitList($model, $unitLit, $newRecord)
    {
        if ($newRecord) {
            for ($i = 0; $i < sizeof($unitLit); $i++) {
                $item = json_decode($unitLit[$i]);
                $subItem = new CusCommodityProfile();
                $subItem->commodity_id = $model->id;
                $subItem->name = $item->unit_unit;
                $subItem->price = $item->unit_price;
                $subItem->desc = $item->unit_desc;
                $subItem->is_basics_unit = $item->unit_is_basics_unit;
                $subItem->base_self_ratio = $item->unit_base_self_ratio;
                //$subItem->tag = $item->tag;
                $subItem->is_sell = $item->unit_is_sell ? '1' : '0';
                if (!$subItem->validate()) {
                    throw new Exception(implode(",", $subItem->errors));
                }
                $subItem->save();
            }
        }

    }
}