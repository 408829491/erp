<?php

namespace app\models\form;

use app\models\CusDiscountCoupon;
use app\models\CusDiscountCouponGetRecord;
use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;

class CusReportsForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $user;
    public $source;
    public $storeId;
    public $create_time;
    public $status_text = [
        1 => '待发货',
        2 => '待收货',
        3 => '已完成',
        4 => '已关闭',
    ];
    public $pay_text = [
        'N' => '未付款',
        'Y' => '已付款',
    ];
    public $source_text = [
        1 => '小程序',
        2 => 'App',
        3 => '后台'
    ];
    public $settlement_type = [
        '1' => '销售订单',
        '2' => '运费',
    ];

    public function init()
    {
        parent::init();
        $this->storeId = Yii::$app->user->identity['store_id'];
    }

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['source',], 'default', 'value' => ''],
            [['is_pay',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询订单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = CusOrder::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'user_name', $this->keyword],
                ['like', 'receive_tel', $this->keyword],
                ['like', 'nick_name', $this->keyword],
                ['like', 'order_no', $this->keyword],
            ]);
        }
        if ($this->delivery_date) {
            $query->andwhere([
                'and',
                [
                    '>=', 'delivery_date',
                    $this->dateFormat($this->delivery_date)['begin_date']
                ],
                [
                    '<=', 'delivery_date',
                    $this->dateFormat($this->delivery_date)['end_date']
                ],
            ]);
        }
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        if (!empty($this->is_pay)) {
            $query->andWhere([
                'is_pay' => $this->is_pay
            ]);
        }
        if (!empty($this->source)) {
            $query->andWhere([
                'source' => $this->source
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as $k => &$v) {
            $v['pay_text'] = $this->pay_text[$v['is_pay']];
            $v['status_text'] = $this->status_text[$v['status']];
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 获取客户下单列表数据
     * @param $userId
     * @return array
     */
    public function getTradeData()
    {
        $week = array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
        $this->attributes = Yii::$app->request->get();
        $query = CusOrder::find();
        $query->where(['store_id'=>$this->storeId]);
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $list = $query
            ->select('FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,count(id) as order_count,sum(quantity) as commodity_count,sum(pay_price) as total_price,sum(total_profit) as total_profit,(FROM_UNIXTIME(create_time,("%w"))) as week')
            ->asArray()
            ->groupBy('date')
            ->all();
        foreach ($list as $k => &$v) {
            $v['id'] = $k + 1;
            $v['date'] = $v['date'].' ('.$week[$v['week']].')';
            $v['total_profit_radio'] = round(($v['total_profit'] / $v['total_price'] * 100), 2) . '%';
        }
        return [
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 获取订单商品汇总数据
     * @return mixed
     */
    public function getGoodsSaleData()
    {
        $this->attributes = Yii::$app->request->get();
        $query = CusOrderDetail::find();
        $query->where(['source_type_id' => 3,'bn_cus_order_detail.store_id'=>$this->storeId]);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'commodity_name', $this->keyword],
                ['like', 'product_code', $this->keyword],
            ]);
        }
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $query->select('bn_cus_commodity.sell_stock,commodity_id,bn_cus_commodity_category.name as type_name,commodity_name,sum(num) as total_num,count(order_id) as order_counts,product_code,bn_cus_order_detail.unit,sum(total_price) as total_price,sum(total_profit) as total_profit,group_concat(order_id) as order_ids')
            ->leftJoin('bn_cus_commodity', 'bn_cus_commodity.uid = bn_cus_order_detail.commodity_id')
            ->leftJoin('bn_cus_commodity_category', 'bn_cus_commodity_category.pid = bn_cus_order_detail.type_id')
            ->groupBy('commodity_id')
            ->asArray();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();
        foreach ($data as &$v) {
            if ($v['total_price'] > 0) {
                $v['profit_ratio'] = round((($v['total_profit'] / $v['total_price']) * 100), 2) . '%';
            }

        }
        return [
            'total' => $count,
            'list' => $data,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }


    /**
     * 获取订单商品汇总数据
     * @return mixed
     */
    public function getSaleElementData()
    {
        $this->attributes = Yii::$app->request->get();
        $query = CusOrderDetail::find();
        $query->where(['source_type_id' => 3,'bn_cus_order_detail.store_id'=>$this->storeId]);
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $query->select('bn_cus_commodity.price,bn_cus_commodity.sell_stock,commodity_id,bn_cus_commodity_category.id as cid,bn_cus_commodity_category.name as type_name,commodity_name,sum(num) as total_num,count(order_id) as order_counts,product_code,bn_cus_order_detail.unit,sum(total_price) as total_price,sum(total_profit) as total_profit,group_concat(order_id) as order_ids')
            ->leftJoin('bn_cus_commodity', 'bn_cus_commodity.uid = bn_cus_order_detail.commodity_id')
            ->leftJoin('bn_cus_commodity_category', 'bn_cus_commodity_category.id = bn_cus_commodity.type_first_tier_id')
            ->groupBy('cid')
            ->asArray();
        $count = $query->count();
        $data = $query->all();
        $amount = array_sum(array_column($data,'total_price'));
        foreach ($data as $k => &$v) {
            if ($v['total_price'] > 0) {
                $v['profit_ratio'] = round((($v['total_profit'] / $v['total_price']) * 100), 2) . '%';
                $v['unit_price'] = round(($v['total_price'] / $v['order_counts']), 2);
                $v['price_radio'] = round((($v['total_price'] / $amount) * 100), 2) . '%';
            }
            $v['id'] = $k + 1;
            $v['type_name'] =  $v['type_name']?$v['type_name']:'无';
            $v['price'] =  $v['price']?$v['price']:'0.00';
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
