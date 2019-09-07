<?php

namespace api\modules\v2\controllers;

use app\models\CusCommodity;
use app\models\CusCommodityProfile;
use app\models\CusSeckill;
use app\models\CusSeckillCommodity;
use app\models\CusShopcart;
use app\models\form\CommodityForm;
use app\models\form\CusCommodityCategoryForm;
use app\models\form\CusShopcartForm;
use backend\responses\ApiResponse;
use Yii;

/**
 * C端购物车
 */
class CusShopcartController extends Controller
{
    // 查询购物车的数量
    public function actionFindTotalNum() {
        $data = CusShopcart::find()
            ->where(['store_id' => Yii::$app->request->post('storeId')])
            ->andWhere(['cus_member_id' => $this->user['id']])
            ->all();

        $totalNum = 0;
        foreach ($data as $item) {
            $totalNum += $item->num;
        }

        return $totalNum;
    }

    // 保存购物车
    public function actionSaveShopcart() {
        $data = Yii::$app->request->post('data');
        $storeId = Yii::$app->request->post('storeId');

        if ($data != null) {
            $data = json_decode($data, true);
            $cusShopCartFrom = new CusShopcartForm();
            $cusShopCartFrom->save($data, $storeId, $this->user->id);
        }
        return new ApiResponse();
    }

    // 添加购物车,如果已经存在 + 1，如果参数有数量添加指定数量
    public function actionAdd() {
        $is_basics_unit = Yii::$app->request->post('is_basics_unit');
        $type = \Yii::$app->request->post('type');
        $sourceId = \Yii::$app->request->post('source_id');
        $unit = \Yii::$app->request->post('unit');
        $storeId = \Yii::$app->request->post('store_id');
        $cusMemberId = $this->user['id'];
        $base_self_ratio = Yii::$app->request->post('base_self_ratio');
        $num = Yii::$app->request->post('num');

        if ($is_basics_unit == 1) {
            // 是基础单位
            $query = CusShopcart::find()
                ->where(['store_id' => $storeId, 'cus_member_id' => $cusMemberId, 'type' => $type, 'source_id' => $sourceId, 'unit' => $unit, 'is_basics_unit' => 1])
                ->one();
        } else {
            $query = CusShopcart::find()
                ->where(['store_id' => $storeId, 'cus_member_id' => $cusMemberId, 'type' => $type, 'source_id' => $sourceId, 'unit' => $unit, 'is_basics_unit' => 0, 'base_self_ratio' => $base_self_ratio])
                ->one();
        }

        if ($query == null) {
            $model = new CusShopcart();
            $model->attributes = \Yii::$app->request->post();
            $model->cus_member_id = $cusMemberId;

            if ($num == null) {
                $model->num = 1;
            } else {
                $model->num = $num;
            }

            $model->save();
        } else {

            if ($num == null) {
                $query->num += 1;
            } else {
                $query->num += $num;
            }

            $query->save();
        }

        return new ApiResponse();
    }

    // 根据用户id和店铺id查询购物车
    public function actionFindListByCusMemberIdAndStoreId() {
        $storeId = Yii::$app->request->post('storeId');
        $data = CusShopcart::find()->asArray()
            ->where(['store_id' => $storeId, 'cus_member_id' => $this->user['id']])
            ->all();

        // 查询分类数据获取名称
        $classQuery = new CusCommodityCategoryForm();
        $classData = $classQuery->getCategoryList($storeId);

        $usableData = [];
        $disabledData = [];
        foreach ($data as $item) {
            if ($item['type'] == 0) {
                // 普通商品
                $data2 = CusCommodity::findOne(['id' => $item['source_id'], 'is_online' => 1]);

                if ($data2 != null) {
                    // 查询对应的单位
                    $data3 = CusCommodityProfile::find()->asArray()
                        ->where(['commodity_id' => $data2 -> id])
                        ->all();

                    if ($data3 != null) {
                        $flag = false;

                        foreach ($data3 as $index => $item2) {
                            if ($item['is_basics_unit'] == 1) {
                                if ($item2['name'] == $item['unit']) {
                                    $item['price'] = $item2['price'];
                                    $item['selectTypeIndex'] = $index;
                                    $flag = true;
                                }
                            } else {
                                if ($item2['name'] == $item['unit'] && $item2['base_self_ratio'] == $item['base_self_ratio']) {
                                    $item['price'] = $item2['price'];
                                    $item['selectTypeIndex'] = $index;
                                    $flag = true;
                                }
                            }
                        }

                        if ($flag) {
                            $item['name'] = $data2->name;
                            $item['type_first_tier_id'] = $data2->type_first_tier_id;
                            $item['type_id'] = $data2->type_id;
                            $item['parent_type_name'] = $classData[$item['type_id']]['parent_name'];
                            $item['type_name'] = $classData[$item['type_id']]['name'];
                            $item['units'] = $data3;
                            $item['slide_x'] = 0;

                            array_push($usableData, $item);
                        } else {
                            array_push($disabledData, $item);
                        }
                    } else {
                        array_push($disabledData, $item);
                    }
                } else {
                    array_push($disabledData, $item);
                }
            } else if ($item['type'] == 1) {
                // 促销
                $data2 = CusSeckillCommodity::findOne($item['source_id']);

                if ($data2 != null) {
                    // 查询是否过期
                    $data3 = CusSeckill::find()->asArray()
                        ->where(['is_close' => 0, 'id' => $data2 -> cus_seckill_id])
                        ->andWhere(['<=', 'start_time', date('Y-m-d H:i:s')])
                        ->andWhere(['>=', 'end_time', date('Y-m-d H:i:s')])
                        ->one();

                    if ($data3 != null) {
                        $item['slide_x'] = 0;
                        $item['pic'] = explode(':;', $item['pic'])[0];
                        $item['units'] = [['name' => $item['unit'], 'price' => $item['price'], 'is_basics_unit' => 1]];
                        $item['selectTypeIndex'] = 0;

                        array_push($usableData, $item);
                    } else {
                        array_push($disabledData, $item);
                    }
                } else {
                    array_push($disabledData, $item);
                }
            }
        }

        return ['usableData' => $usableData, 'disabledData' => $disabledData];
    }

    // 加载购物车的推荐数据
    public function actionRecommendData() {
        $storeId = Yii::$app->request->post('storeId');
        // 加载推荐数据
        $data2 = CusCommodity::find()->asArray()
            ->where(['store_id' => $storeId])
            ->andWhere('pic is not null')
            ->andWhere('is_online = 1')
            ->orderBy('rand()')
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

        return $data2;
    }
}