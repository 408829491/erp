<?php

namespace app\models\form;

use app\models\Seckill;
use app\models\SeckillCommodity;

class SeckillCommodityForm extends \yii\base\Model
{
    // 根据id查找商品
    public function findOneById($id)
    {
        $data = SeckillCommodity::find()->asArray()
            ->select('id, price, activity_price, limit_buy, unit, name, pic, type_id, brand,seckill_id, summary, commodity_id')
            ->where(['id'=>$id])
            ->one();

        // 查询限时活动
        $data1 = Seckill::find()->asArray()
            ->select('id,name,is_limit_buy_num,end_time')
            ->where(['id'=>$data['seckill_id']])
            ->one();

        $data['is_limit_buy_num'] = $data1['is_limit_buy_num'];
        $data['end_time'] = strtotime($data1['end_time']) * 1000;

        return $data;
    }
}