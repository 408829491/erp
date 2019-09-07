<?php

namespace app\models\form;

use app\models\Seckill;
use app\models\SeckillCommodity;

class SeckillForm extends \yii\base\Model
{
    // 获取当天的活动
    public function findOneByToday() {
        $model = Seckill::find()
            ->andWhere(['is_close'=>0])
            ->andWhere([' <= ', 'start_time', date('y-m-d H:i:s', time())])
            ->andWhere([' >= ', 'end_time', date('y-m-d H:i:s', time())])
            ->orderBy('id DESC')
            ->limit('1')
            ->asArray()
            ->one();

        // 结束时间转成时间戳
        if (!empty($model)) {
            $model['end_time'] = strtotime($model['end_time']) * 1000;

            // 查询子表数据
            $model['subList'] = SeckillCommodity::find()
                ->select('id,name,pic,unit,price,activity_price,commodity_id')
                ->where(['seckill_id'=>$model['id']])
                ->asArray()
                ->all();

            foreach ($model['subList'] as $value=>&$item) {
                $item['pic'] = explode(':;', $item['pic'])[0];
            }

        }
        return $model;
    }
}