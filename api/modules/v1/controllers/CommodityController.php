<?php

namespace api\modules\v1\controllers;


use app\models\CommodityProfile;
use app\models\form\CommodityForm;
use app\models\form\SeckillCommodityForm;
use app\models\form\SeckillForm;

/**
 * 商品Controller
 */

class CommodityController extends Controller
{
    // 获取首页的内容
    public function actionIndexPage ()
    {
        // 获取今日的优惠活动
        $model= new SeckillForm();
        $data = $model->findOneByToday();

        // 获取新品推荐（展示16个）
        $model1 = new CommodityForm();
        $data1 = $model1->findNewCommodity($this->user);

        return  ['seckillByToday'=>$data, 'newCommodity'=>$data1];
    }

    // 获取商品详情
    public function actionFindOneById($id) {
        $model = new CommodityForm();
        $data = $model->findOneById($id);

        // 组装图片
        $picsData = explode(':;',$data['pic']);
        foreach ($picsData as &$v) {
            $v = $v.'?x-oss-process=image/resize,h_500';
        }

        // 查询所有单位（可卖）
        $units = CommodityProfile::find()->asArray()
            ->select('bn_commodity_profile.id, bn_commodity_profile.commodity_id, bn_commodity_profile.name, bn_commodity_profile.is_basics_unit, bn_commodity_profile.base_self_ratio, bn_commodity_profile_detail.price')
            ->leftJoin('bn_commodity_profile_detail', 'bn_commodity_profile_detail.commodity_profile_id = bn_commodity_profile.id and bn_commodity_profile_detail.type_id = '.$this->user->c_type_id)
            ->where(['bn_commodity_profile.is_sell'=>1,'bn_commodity_profile.commodity_id'=>$id])
            ->all();

        // 单位的名称重新组织
        foreach ($units as &$v) {
            if ($v['is_basics_unit'] == 1) {
                $v['showName'] = '1'.$v['name'];
            } else {
                $v['showName'] = '1'.$v['name'].'('.$v['base_self_ratio'].$data['unit'].')';
            }
        }

        // 猜你喜欢数据
        $youLikeData = $model->findListByRandom($data['type_id'], $this->user, $id);

        return ['commodity'=>$data, 'pics'=>$picsData,'units'=>$units, 'youLikeData'=>$youLikeData];
    }

    // 获取限时商品详情
    public function actionFindOneByIdAndLimit($id) {
        $model = new SeckillCommodityForm();
        $data = $model->findOneById($id);

        // 组装图片
        $picsData = explode(':;',$data['pic']);
        foreach ($picsData as &$v) {
            $v = $v.'?x-oss-process=image/resize,h_500';
        }

        // 拼接单位数据
        $units = [['showName'=>'1'.$data['unit'], 'name'=>$data['unit'], 'price'=>$data['activity_price']]];

        // 猜你喜欢数据
        $commodityForm = new CommodityForm();
        $youLikeData = $commodityForm->findListByRandom($data['type_id'], $this->user, $data['commodity_id']);

        return ['commodity'=>$data, 'pics'=>$picsData,'units'=>$units, 'youLikeData'=>$youLikeData];
    }

    // 搜索商品
    public function actionFindListByName($pageNum=1,$pageSize=10) {
        $commodityForm = new CommodityForm();
        return $commodityForm->findPage($pageNum, $pageSize, null, \Yii::$app->request->get('isSeckill'), \Yii::$app->request->get('order'), \Yii::$app->request->get('name'), $this->user);
    }

    // 核对购物商品的价格以及促销商品是否过期
    public function actionCheckCommodityList() {
        $commodityForm = new CommodityForm();
        return $commodityForm->checkCommodity(\Yii::$app->request->post('shopCartDataList'), $this->user);
    }
}
