<?php

namespace api\modules\v2\controllers;

use app\models\CusCommodity;
use app\models\CusCommodityCategory;
use app\models\CusCommodityProfile;
use app\models\CusGroup;
use app\models\CusGroupCommodity;
use app\models\CusSeckill;
use app\models\CusSeckillCommodity;
use app\models\CusStore;
use app\models\CusStoreSlideshow;
use app\utils\GeoDeUtils;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

/**
 * C端店铺
 */
class CusStoreController extends Controller
{

    public function behaviors() {
        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'optional' =>[
                    'find-list-by-location',
                    'find-store-by-id',
                    'find-commodity-category-by-id',
                    'find-all'
                ]
            ],
        ]);
    }

    // 查询
    public function actionFindStoreById() {
        return CusStore::findOne(\Yii::$app->request->post('id'));
    }

    // 获取所有店铺和自提柜并计算距离
    public function actionFindAll() {
        $data = CusStore::find()->all();
        $storeDataList = [];
        $deviceDataList = [];
        foreach ($data as $item) {
            if ($item->type == 0) {
                array_push($storeDataList, $item);
            } else if ($item->type == 1) {
                array_push($deviceDataList, $item);
            }
        }

        return ['storeDataList' => $storeDataList, 'deviceDataList' => $deviceDataList];
    }

    // 10s切换的秒杀, 秒杀过时
    public function actionFindOneByUsableOnlySwitch() {
        $id = \Yii::$app->request->post('storeId');

        // 查询限时秒杀
        $seckillData = CusSeckill::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere([' <= ', 'start_time', date('y-m-d H:i:s', time())])
            ->andWhere([' >= ', 'end_time', date('y-m-d H:i:s', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();

        if (!empty($seckillData)) {
            // 随机返回三个
            $randData = CusSeckillCommodity::find()->asArray()
                ->select('id, name, pic, unit, price, activity_price, cus_commodity_id, summary')
                ->where(['cus_seckill_id'=>$seckillData['id']])
                ->orderBy('rand()')
                ->limit(3)
                ->all();

            foreach ($randData as &$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];
            }

            $seckillData['randData'] = $randData;
        }

        return $seckillData;
    }

    // 加载可用的秒杀
    public function actionFindOneByUsable() {
        $id = \Yii::$app->request->post('storeId');

        // 查询限时秒杀
        $seckillData = CusSeckill::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere([' <= ', 'start_time', date('y-m-d H:i:s', time())])
            ->andWhere([' >= ', 'end_time', date('y-m-d H:i:s', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();

        // 结束时间转成时间戳
        if (!empty($seckillData)) {
            // 查询子表数据
            $seckillData['subList'] = CusSeckillCommodity::find()->asArray()
                ->select('id, name, pic, unit, price, activity_price, cus_commodity_id, summary')
                ->where(['cus_seckill_id'=>$seckillData['id']])
                ->all();

            foreach ($seckillData['subList'] as &$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];

                if (empty($item['summary'])) {
                    $item['summary'] = '时价好物，实惠亲民';
                }
            }

            // 随机返回三个
            $randData = CusSeckillCommodity::find()->asArray()
                ->select('id, name, pic, unit, price, activity_price, cus_commodity_id, summary')
                ->where(['cus_seckill_id'=>$seckillData['id']])
                ->orderBy('rand()')
                ->limit(3)
                ->all();

            foreach ($randData as &$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];
            }

            $seckillData['randData'] = $randData;
        }

        return $seckillData;
    }

    // 加载今日和明日的团购
    public function actionFindGroupByTodayAndTomorrow() {
        $id = \Yii::$app->request->post('store_id');

        // 查询今日的团购
        $groupData = CusGroup::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere(['date' => date('Y-m-d', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();
        // 团购商品
        if (!empty($groupData)) {
            // 查询子表数据
            $groupData['subList'] = CusGroupCommodity::find()->asArray()
                ->select('id, name, pic, unit, price, activity_price, cus_commodity_id, success_num')
                ->where(['cus_group_id'=>$groupData['id']])
                ->all();

            foreach ($groupData['subList'] as &$item) {
                if (empty($item['summary'])) {
                    $item['summary'] = '时价好物，实惠亲民';
                }

                $item['pic'] = explode(':;', $item['pic'])[0];
            }
        }

        // 查询明日团购
        $groupData2 = CusGroup::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere(['date' => date('Y-m-d', time() + 24 * 60 * 60)])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();
        // 团购商品
        if (!empty($groupData2)) {
            // 查询子表数据
            $groupData2['subList'] = CusGroupCommodity::find()->asArray()
                ->select('id, name, pic, unit, price, activity_price, cus_commodity_id, success_num')
                ->where(['cus_group_id'=>$groupData2['id']])
                ->all();

            foreach ($groupData2['subList'] as &$item) {
                if (empty($item['summary'])) {
                    $item['summary'] = '时价好物，实惠亲民';
                }

                $item['pic'] = explode(':;', $item['pic'])[0];
            }
        }

        return ['todayData' => $groupData, 'tomorrowData' => $groupData2];
    }

    // 加载今日和明日的团购
    public function actionFindGroupByTodayAndTomorrowNew() {
        $id = \Yii::$app->request->post('storeId');
        // 查询团购主表
        $groupData = CusGroup::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere([' >= ', 'start_time', date('Y-m-d', time()).' 00:00:00'])
            ->andWhere([' <= ', 'end_time', date('Y-m-d', time() + 24 * 60 * 60).' 23:59:59'])
            ->orderBy('start_time')
            ->all();

        // 判断团购是否开启
        $groupId = -1;
        foreach ($groupData as &$item) {
            if (strtotime($item['start_time']) <= time()) {
                // 已开启
                $item['isStart'] = 1;
                $item['endTimestamp'] = strtotime($item['end_time']);
                if ($groupId == -1) {
                    $groupId = $item['id'];
                }
            } else {
                $item['isStart'] = 0;
                // 判断是今日还是明日
                if (explode(' ', $item['start_time'])[0] == date('Y-m-d', time())) {
                    $item['showTime'] = substr($item['start_time'], 11, 5);
                } else {
                    $item['showTime'] = '明日'.substr($item['start_time'], 11, 5);
                }
            }
        }

        // 加载最近的开启的团购数据的明细
        $details = [];
        if ($groupId != -1) {
            $details = CusGroupCommodity::find()->asArray()
                ->where(['cus_group_id' => $groupId])
                ->all();

            foreach ($details as &$item) {
                $item['summary'] = $item['summary'] == null ? '时价好物，实惠亲民' : $item['summary'];
            }
        }

        return ['times' => $groupData, 'details' => $details];
    }

    // 根据团购id加载明细
    public function actionFindGroupCommodity($id) {
        if ($id == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $details = CusGroupCommodity::find()->asArray()->where(['cus_group_id' => $id])->all();

        foreach ($details as &$item) {
            $item['summary'] = $item['summary'] == null ? '时价好物，实惠亲民' : $item['summary'];
        }

        return $details;
    }

    // 根据门店id查询分类，限时抢购，超值拼团，热卖推荐
    public function actionFindCommodityCategoryById() {
        $id = \Yii::$app->request->post('id');
        if ($id == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        // 查询轮播图
        $cusStoreSlideshowData = CusStoreSlideshow::find()
            ->where(['store_id' => $id])
            ->all();

        // 查询分类
        $data = CusCommodityCategory::find()->asArray()
            ->where(['pid'=>0, 'store_id'=>$id])
            ->andWhere(['is_show' => 1])
            ->orderBy('sequence DESC')
            ->limit(8)
            ->all();

        // 查询限时秒杀
        $seckillData = CusSeckill::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere([' <= ', 'start_time', date('Y-m-d H:i:s', time())])
            ->andWhere([' >= ', 'end_time', date('Y-m-d H:i:s', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();

        // 结束时间转成时间戳
        if (!empty($seckillData)) {
            $seckillData['end_time'] = strtotime($seckillData['end_time']) * 1000;

            // 查询子表数据
            $seckillData['subList'] = CusSeckillCommodity::find()->asArray()
                ->select('id,name,pic,unit,price,activity_price,cus_commodity_id')
                ->where(['cus_seckill_id'=>$seckillData['id']])
                ->limit(6)
                ->all();

            foreach ($seckillData['subList'] as &$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];
            }
        }

        // 查询今日的团购
        $groupData = CusGroup::find()->asArray()
            ->where(['store_id'=>$id])
            ->andWhere(['is_close'=>0])
            ->andWhere(['date' => date('Y-m-d', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();
        // 团购商品
        if (!empty($groupData)) {
            // 查询子表数据
            $groupData['subList'] = CusGroupCommodity::find()->asArray()
                ->select('id,name,pic,unit,price,activity_price,cus_commodity_id')
                ->where(['cus_group_id'=>$groupData['id']])
                ->limit(2)
                ->all();

            foreach ($groupData['subList'] as &$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];
            }
        }

        // 查询热卖商品
        $data2 = CusCommodity::find()->asArray()
            ->Where(['like', 'tag', '%热销%', false])
            ->andWhere(['store_id'=>$id])
            ->andWhere(['is_online' => 1])
            ->andWhere('pic is not null')
            ->limit(24)
            ->all();

        foreach ($data2 as &$v) {
            if (empty($v['summary'])) {
                $v['summary'] = '时价好物，实惠亲民';
            }
            // 只取第一张图片
            $v['pic']= explode(':;', $v['pic'])[0];
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

        return ['slideshowData' => $cusStoreSlideshowData, 'commodityCategory' => $data, 'hotCommodityData' => $data2, 'seckillData' => $seckillData, 'groupData' => $groupData];
    }

    // 根据地址查询列表 计算距离
    public function actionFindListByLocation() {
        $lng = \Yii::$app->request->post('lng');
        $lat = \Yii::$app->request->post('lat');

        if ($lng == null || $lat == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $query = CusStore::find()->asArray();
        $type = \Yii::$app->request->post('type');
        if ($type == null) {
            $type = 0;
        }
        $query->andWhere(['type'=>$type]);
        // 添加门店名称搜索
        $keyword = \Yii::$app->request->post('keyword');
        if ($keyword != null) {
            $query->andWhere(['like', 'name', "%$keyword%", false]);
        }

        $data = $query->all();

        $flag = false;
        // 计算距离 并且按照远近排序 计算是否有可用的地址并且拼接距离
        foreach ($data as &$v) {
            $v['distance'] = GeoDeUtils::calculateDistance($v['lng'], $v['lat'], $lng, $lat);
            $v['grade'] = 5;

            // 计算是否有可用的地址
            if ($v['distance'] <= $v['limit_delivery_meter']) {
                $flag = true;
            }

            if ($v['distance'] > 1000) {
                $v['distanceShow'] = (floor($v['distance'] / 100) / 10).'km';
            } else {
                $v['distanceShow'] = (floor($v['distance'])).'m';
            }
        }
        for ($i = 0; $i < count($data) - 1; $i ++) {
            $mix = $i;
            for ($j = $i + 1; $j < count($data); $j ++) {
                if (((double)$data[$mix]['distance']) > ((double)$data[$j]['distance'])) {
                    $mix = $j;
                }
            }
            $tempData = $data[$i];
            $data[$i] = $data[$mix];
            $data[$mix] = $tempData;
        }

        // 根据坐标获取地址
        $addressData = GeoDeUtils::getAddress($lng, $lat);
        $formattedAddress = $addressData['regeocode']['formatted_address'];
        $district = $addressData['regeocode']['addressComponent']['district'];
        return ['dataList'=>$data, 'is_usable'=>$flag, 'addressData'=>substr($formattedAddress, strpos($formattedAddress, $district) + strlen($district), strlen($formattedAddress))];
    }
}