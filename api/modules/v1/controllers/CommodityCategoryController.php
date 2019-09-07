<?php

namespace api\modules\v1\controllers;


use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\form\CommodityForm;

/**
 * 商品分类Controller
 */

class CommodityCategoryController extends Controller
{
    // 获取分类所有数据
    public function actionFindClass() {
        // 获取分类的所有数据
        $data = CommodityCategory::find()
            ->select('id,pid,name')
            //->where(['in', 'id', [73,74]])
            ->asArray()
            ->orderBy('pid, sequence')
            ->all();

        // 组织成分层数据
        $tempData = [];
        foreach ($data as $value) {
            if ($value['pid'] == 0) {
                array_push($tempData, $value);
            } else {
                foreach ($tempData as $k => &$v) {
                    if ($v['id'] == $value['pid']) {
                        if (!isset($v['subList'])) {
                            $v['subList'] = [];
                        }
                        array_push($v['subList'], $value);
                        break;
                    }
                }
            }
        }

        // 获取第一层第一个分类的列表数据
        $commodityForm = new CommodityForm();

        return ['firstClassData'=>$tempData, 'firstClassHeight'=>108 + 110 * (count($tempData)-1), 'secondClassData'=>$tempData[0]['subList'], 'listData'=>$commodityForm->findPage(1,10,$tempData[0]['subList'][0]['id'], null, null, null, $this->user)];
    }

    // 获取指定二级分类的商品数据
    public function actionFindListBySecondPage($pageNum=1,$pageSize=10) {
        $commodityForm = new CommodityForm();
        return $commodityForm->findPage($pageNum, $pageSize, \Yii::$app->request->get('type_id'), \Yii::$app->request->get('isSeckill'), \Yii::$app->request->get('order'), null, $this->user);
    }

    // 新品需求的分类
    public function actionFindClassByNewNeed () {
        // 获取分类的所有数据
        $data = CommodityCategory::find()
            ->select('id,pid,name')
            ->asArray()
            ->all();

        // 组织成分层数据
        $tempData = [];
        foreach ($data as $value) {
            if ($value['pid'] == 0) {
                $tempParent = [];
                $tempParent['value'] = intval($value['id']);
                $tempParent['label'] = $value['name'];
                array_push($tempData, $tempParent);
            } else {
                foreach ($tempData as $k => &$v) {
                    if ($v['value'] == $value['pid']) {
                        if (!isset($v['children'])) {
                            $v['children'] = [];
                        }
                        $tempParent2 = [];
                        $tempParent2['value'] = intval($value['id']);
                        $tempParent2['label'] = $value['name'];
                        array_push($v['children'], $tempParent2);
                        break;
                    }
                }
            }
        }

        return $tempData;
    }
}
