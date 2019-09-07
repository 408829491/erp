<?php

namespace app\models\form;

use app\models\CusGroup;
use app\models\CusGroupCommodity;
use yii\base\Exception;
use yii\data\Pagination;

class CusGroupForm {


    public function findPage($pageNum=0,$pageSize=10,$filterProperty,$select)
    {
        $pageNum -= 1;
        $query = CusGroup::find();

        if ($select != null) {
            $query->select([$select]);
        }

        $storeId = \Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => \Yii::$app->user->identity['store_id']]);
        }

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty);
            $typeId = $json->typeId;
            if ($typeId != null) {
                $query->andWhere(['type_first_tier_id' => $typeId]);
            }
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $data['list'] = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return $data;
    }

    // 保存数据
    public function saveData($model, $subList) {
        $transaction  = CusGroup::getDb()->beginTransaction();

        try {

            if (!$model->validate()) {
                throw new Exception(implode(",", $model->errors));
            };
            $model->save();

            if (sizeof($subList) != 0) {
                foreach ($subList as $value) {
                    $model1 = new CusGroupCommodity();
                    $model1->attributes = $value;
                    $model1->store_id = $model->store_id;
                    $model1->cus_group_id = $model->id;
                    if (!$model1->validate()) {
                        throw new Exception(implode(",", $model1->errors));
                    };
                    $model1->save();
                }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 修改数据
    public function editData($model, $subList) {
        $transaction  = CusGroup::getDb()->beginTransaction();

        try {

            if (!$model->validate()) {
                throw new Exception(implode(",", $model->errors));
            };
            $model->save();

            // 删除子表
            CusGroupCommodity::deleteAll(['cus_group_id' => $model->id]);
            // 保存子表
            self::saveSubList($model, $subList);

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function saveSubList($model, $subList) {
        if (sizeof($subList) != 0) {
            foreach ($subList as $value) {
                $model1 = new CusGroupCommodity();
                $model1->attributes = $value;
                $model1->cus_group_id = $model->id;
                $model1->store_id = \Yii::$app->user->identity['store_id'];
                if (!$model1->validate()) {
                    throw new Exception(implode(",", $model1->errors));
                };

                $model1->save();
            }
        }
    }

    // 获取当天的活动
    public function findOneByToday() {
        $model = CusGroup::find()->asArray()
            ->andWhere('0', 'is_close')
            ->andWhere([' <= ', 'start_time', date('Y-m-d H:i:s')])
            ->andWhere([' >= ', 'end_time', date('Y-m-d H:i:s')])
            ->orderBy('id DESC')
            ->limit('1')
            ->one();
        return $model;
    }
}