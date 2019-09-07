<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/4/28
 * Time: 10:03
 */

namespace app\models\form;

use app\models\Model;
use app\models\OrderDetail;
use Yii;

class BatchSummaryForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $delivery_date;
    public $type_id;
    public $channel_type;
    public $create_time;
    public $type_first_tier_id;


    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 1,],
            [['source',], 'default', 'value' => ''],
            [['type_id',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
            [['type_first_tier_id',], 'default', 'value' => ''],
            [['channel_type',], 'default', 'value' => ''],
            [['channel_type',], 'default', 'value' => ''],
        ];
    }

    /**
     * 订单汇总
     * @return mixed
     */
    public function getOrderSummaryList()
    {
        $this->attributes = Yii::$app->request->get();
        $query = OrderDetail::find();
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'user_name', $this->keyword],
                ['like', 'receive_tel', $this->keyword],
                ['like', 'driver_name', $this->keyword],
            ]);
        }
        if ($this->delivery_date) {
            $query->andwhere(['bn_order_detail.delivery_date' => $this->delivery_date]);
        } else {
            $query->andwhere(['bn_order_detail.delivery_date' => date("Y-m-d", strtotime("+1 day"))]);
        }

        if ($this->type_first_tier_id) {
            $query->andwhere(['type_first_tier_id' => $this->type_first_tier_id]);
        }

        if ($this->channel_type) {
            $query->andwhere(['channel_type' => $this->channel_type]);
        }

        $data = $query
            ->asArray()
            ->select('commodity_id,commodity_name,sum(num) as total_num,count(order_id) as order_counts,bn_order_detail.price,bn_order_detail.in_price,unit,group_concat(order_id) as order_ids,group_concat(order_id) as order_ids,agent_id,agent_name,channel_type')
            ->leftJoin('bn_order','bn_order.id=bn_order_detail.order_id')
            ->andWhere(['bn_order.status'=>1])
            ->groupBy('commodity_id')
            ->all();
        $count = $query->count();
        foreach ($data as &$v) {
            $v['stock_num'] = 0;
            $v['need_purchase_num'] = $v['total_num'] - $v['stock_num'];
            $v['is_purchase_num'] = 0;
        }
        return [
            'total' => $count,
            'list' => $data,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval, $timestamp = 1)
    {
        $date_interval = explode(' - ', $date_interval);
        return [
            'begin_date' => $timestamp ? strtotime($date_interval[0]) : $date_interval[0],
            'end_date' => $timestamp ? strtotime($date_interval[1]) : $date_interval[1]
        ];
    }
}