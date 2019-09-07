<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Purchase;
use app\models\PurchaseDetail;
use app\models\StockInDetail;
use app\models\StockLossOverflowDetail;
use app\models\StockOutDetail;
use Yii;
use yii\data\Pagination;

class ReportsForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $user;
    public $source;
    public $typeId;
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
            [['typeId',], 'default', 'value' => 0],
        ];
    }

    public function init()
    {
        $this->attributes = Yii::$app->request->get();
    }

    /**
     * 查询订单列表
     * @return array
     */
    public function search()
    {
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
     * @return array
     */
    public function getTradeData()
    {
        $week = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        $query = Order::find();
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
            ->select('FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,count(id) as order_count,sum(price) as total_price,sum(total_profit) as total_profit,(FROM_UNIXTIME(create_time,("%w"))) as week')
            ->asArray()
            ->groupBy('date')
            ->all();
        foreach ($list as $k => &$v) {
            $v['id'] = $k + 1;
            $v['date'] = $v['date'] . ' (' . $week[$v['week']] . ')';
            $v['total_profit_radio'] = round(($v['total_profit'] / $v['total_price'] * 100), 2) . '%';
        }
        $summary = Order::find()
            ->where(['<>', 'status', 4])
            ->select('sum(price) as total_price,count(id) as total_order_count,count(distinct user_id) as total_user_count')
            ->asArray()
            ->one();
        return [
            'total_price' => $summary['total_price'],
            'total_order_count' => $summary['total_order_count'],
            'total_user_count' => $summary['total_user_count'],
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
        $query = OrderDetail::find();
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'commodity_name', $this->keyword],
            ]);
        }
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'bn_order_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_order_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $query->select('bn_commodity.sell_stock,commodity_id,bn_commodity_category.name as type_name,commodity_name,sum(num) as total_num,count(order_id) as order_counts,bn_order_detail.unit,sum(total_price) as total_price,sum(total_profit) as total_profit,group_concat(order_id) as order_ids')
            ->leftJoin('bn_commodity', 'bn_commodity.id = bn_order_detail.commodity_id')
            ->leftJoin('bn_commodity_category', 'bn_commodity_category.id = bn_order_detail.type_id')
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
        $query = OrderDetail::find();
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'bn_order_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_order_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $query->select('bn_commodity.price,bn_commodity.sell_stock,commodity_id,bn_commodity_category.id as cid,bn_commodity_category.name as type_name,commodity_name,sum(num) as total_num,count(order_id) as order_counts,bn_order_detail.unit,sum(total_price) as total_price,sum(total_profit) as total_profit,group_concat(order_id) as order_ids')
            ->leftJoin('bn_commodity', 'bn_commodity.id = bn_order_detail.commodity_id')
            ->leftJoin('bn_commodity_category', 'bn_commodity_category.id = bn_commodity.type_first_tier_id')
            ->groupBy('cid')
            ->asArray();
        $count = $query->count();
        $data = $query->all();
        $amount = array_sum(array_column($data, 'total_price'));
        foreach ($data as $k => &$v) {
            if ($v['total_price'] > 0) {
                $v['profit_ratio'] = round((($v['total_profit'] / $v['total_price']) * 100), 2) . '%';
                $v['unit_price'] = round(($v['total_price'] / $v['order_counts']), 2);
                $v['price_radio'] = round((($v['total_price'] / $amount) * 100), 2) . '%';
            }
            $v['id'] = $k + 1;
            $v['type_name'] = $v['type_name'] ? $v['type_name'] : '无';
            $v['price'] = $v['price'] ? $v['price'] : '0.00';
        }
        return [
            'total' => $count,
            'list' => $data,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 用户统计
     * @return array
     */
    public function getUserStatistics()
    {
        //客户排行
        $orderUser = Order::find();
        $query = $orderUser
            ->select(['id,nick_name,count(id) as total_count,sum(price) as total_price,sum(return_price) as total_refund_price,sum(IF(return_num>0,1,0)) as total_refund_count'])
            ->where(['<>', 'status', 4]);
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
            ->groupBy('user_id')
            ->orderBy('total_count desc')
            ->asArray()
            ->all();
        $order = $refund = $order_price = $refund_price = $summary = 0;
        foreach ($list as $k => &$v) {
            $v['summary'] = $v['total_price'] - $v['total_refund_price'];
            $order += $v['total_count'];
            $refund += $v['total_refund_count'];
            $order_price += $v['total_price'];
            $refund_price += $v['total_refund_price'];
            $v['rank'] = $k + 1;
        }

        return [
            'order' => $order,
            'order_price' => $order_price,
            'refund' => $refund,
            'refund_price' => $refund_price,
            'summary' => $order_price - $refund_price,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 销售员统计
     * @return array
     */
    public function getSalesStatistics()
    {
        //销售员排行
        $orderSalesman = Order::find();
        $query = $orderSalesman->select(['id,salesman_name,count(id) as total_count,COUNT(DISTINCT user_id) as users_count,sum(price) as total_price,sum(return_price) as total_refund_price,sum(IF(return_num>0,1,0)) as total_refund_count'])
            ->where(['<>', 'status', 4]);

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
            ->groupBy('salesman_id')
            ->orderBy('total_count desc')
            ->asArray()
            ->all();
        $order = $refund = $order_price = $refund_price = $summary = $salesman = 0;
        foreach ($list as $k => &$v) {
            $v['summary'] = $v['total_price'] - $v['total_refund_price'];
            $order += $v['total_count'];
            $refund += $v['total_refund_count'];
            $order_price += $v['total_price'];
            $refund_price += $v['total_refund_price'];
            $salesman += 1;
            $v['rank'] = $k + 1;
        }

        return [
            'order' => $order,
            'order_price' => $order_price,
            'refund' => $refund,
            'salesman' => $salesman,
            'refund_price' => $refund_price,
            'summary' => $order_price - $refund_price,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 采购明细汇总
     * @return array
     */
    public function getPurchaseStatistics()
    {
        $purchase = PurchaseDetail::find();
        $query = $purchase
            ->select('purchase_no,bn_purchase.create_time,commodity_name,unit,bn_purchase_detail.purchase_price
                      ,bn_purchase_detail.purchase_num,bn_purchase.purchase_type,bn_stock_in.in_no,bn_stock_in.create_time as in_create_time')
            ->leftJoin('bn_purchase', 'bn_purchase.id=bn_purchase_detail.purchase_id')
            ->leftJoin('bn_stock_in', 'bn_stock_in.about_no = bn_purchase.purchase_no')
            ->asArray();
        $query->where(['bn_purchase.status' => 3]);
        if ($this->create_time) {
            $purchase->andwhere([
                'and',
                [
                    '>=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();
        $model = new PurchaseForm();
        $type = $model->purchase_text;
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['in_create_time'] = date('Y-m-d H:i', $v['in_create_time']);
            $v['total_price'] = round($v['purchase_price'] * $v['purchase_num'], 2);
            $v['purchase_type'] = $type[$v['purchase_type']];
        }
        return [
            'total' => $count,
            'list' => $data,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }


    /**
     * 获取采购统计按明细
     * @return array
     */
    public function getPurchaseSummary()
    {
        $purchaseDetail = PurchaseDetail::find();
        $data = $purchaseDetail
            ->select('count(DISTINCT purchase_id) as total_count,COUNT(DISTINCT type_id) as total_type_count,sum(purchase_total_price) as total_purchase_price,sum(return_num) as total_refund_count,sum(return_num * bn_purchase_detail.purchase_price) as total_refund_price')
            ->leftJoin('bn_purchase', 'bn_purchase.id = bn_purchase_detail.purchase_id')
            ->where(['bn_purchase.status' => 3])
            ->asArray()
            ->one();
        $data['total_price'] = $data['total_purchase_price'] - $data['total_refund_price'];
        return $data;
    }

    /**
     * 获取采购统计按商品
     * @return array
     */
    public function getPurchaseStatisticsByCommodity()
    {
        $purchase = PurchaseDetail::find();
        $query = $purchase
            ->select('commodity_id,unit,bn_purchase_detail.type_name,purchase_no,bn_purchase.create_time,commodity_name,unit,avg(bn_purchase_detail.purchase_price) as average_price,avg(bn_purchase_detail.purchase_price) as average_refund_price,bn_purchase.purchase_type,sum(bn_purchase_detail.purchase_num) as total_count')
            ->leftJoin('bn_purchase', 'bn_purchase.id=bn_purchase_detail.purchase_id')
            ->groupBy('commodity_id')
            ->asArray();
        $query->where(['bn_purchase.status' => 3]);
        if ($this->create_time) {
            $purchase->andwhere([
                'and',
                [
                    '>=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['average_price'] = round($v['average_price'], 2);
            $v['total_price'] = round($v['average_price'] * $v['total_count'], 2);
        }
        return [
            'total' => $count,
            'list' => $data
        ];
    }


    /**
     * 获取采购统计按供应商
     * @return array
     */
    public function getPurchaseStatisticsByProvider()
    {
        $purchase = PurchaseDetail::find();
        $query = $purchase
            ->select('agent_id as id,agent_name,purchase_no,bn_purchase.create_time,commodity_name,unit,bn_purchase_detail.purchase_price
                      ,bn_purchase_detail.purchase_num,bn_purchase.purchase_type,sum(bn_purchase_detail.purchase_num) as total_count,avg(bn_purchase_detail.purchase_price) as average_price')
            ->leftJoin('bn_purchase', 'bn_purchase.id=bn_purchase_detail.purchase_id')
            ->asArray();
        $query->where(['bn_purchase.status' => 3, 'bn_purchase.purchase_type' => 1]);
        if ($this->create_time) {
            $purchase->andwhere([
                'and',
                [
                    '>=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->groupBy('agent_id')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['total_price'] = round($v['purchase_price'] * $v['purchase_num'], 2);
        }
        return [
            'total' => $count,
            'list' => $data
        ];
    }


    /**
     * 获取采购统计按采购员
     * @return array
     */
    public function getPurchaseStatisticsByBuyer()
    {
        $purchase = PurchaseDetail::find();
        $query = $purchase
            ->select('agent_id as id,agent_name,purchase_no,bn_purchase.create_time,commodity_name,unit,bn_purchase_detail.purchase_price
                      ,bn_purchase_detail.purchase_num,bn_purchase.purchase_type,avg(bn_purchase_detail.purchase_price) as average_price,sum(bn_purchase_detail.purchase_num) as total_count')
            ->leftJoin('bn_purchase', 'bn_purchase.id=bn_purchase_detail.purchase_id')
            ->groupBy('agent_id')
            ->asArray();
        $query->where(['bn_purchase.status' => 3, 'bn_purchase.purchase_type' => 0]);
        if ($this->create_time) {
            $purchase->andwhere([
                'and',
                [
                    '>=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_purchase_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();
        $model = new PurchaseForm();
        $type = $model->purchase_text;
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['total_price'] = round($v['purchase_price'] * $v['purchase_num'], 2);
            $v['purchase_type'] = $type[$v['purchase_type']];
        }
        return [
            'total' => $count,
            'list' => $data
        ];
    }

    /**
     * 进销存汇总
     * @return array
     */
    public function getInventoryInvoicingStatistics()
    {
        $this->attributes = Yii::$app->request->get();
        $query = Commodity::find();
        $query->where(['is_online' => 1]);
        if ($this->keyword) {
            $query->andWhere([
                'or',
                ['like', 'bn_commodity.name', $this->keyword],
                ['like', 'alias', $this->keyword],
            ]);
        }
        if ($this->typeId) {
            $query->andwhere(['type_first_tier_id' => $this->typeId]);
        }

        $query->select('bn_commodity.id,bn_commodity.id as commodity_id,bn_commodity.sell_stock,bn_commodity.channel_type,bn_commodity.name,bn_commodity_profile.id as pid,bn_commodity_profile.in_price,type_id,type_first_tier_id,pic,bn_commodity_profile.name as unit,bn_commodity_profile.price,agent_id,agent_name,notice,bn_commodity_profile.is_setting_formula')->leftJoin('bn_commodity_profile', 'bn_commodity_profile.commodity_id=bn_commodity.id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->orderBy('bn_commodity.id DESC')
            ->asArray()
            ->all();
        //分类信息
        $commodityCategory = $this->getCategoryList();
        //销售信息
        $orderInfo = $this->getOrderInfo();
        //入库信息
        $stockInInfo = $this->getStockInInfo();
        //出库信息
        $stockOutInfo = $this->getStockOutInfo();
        //报溢报损信息
        $lossOverflowInfo = $this->getOverflowInfo();

        foreach ($data as $key => &$value) {
            //格式化分类名称（主分类/子分类）
            $id = $value['type_id'];
            $value['type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['name'] : "";
            $value['total_count'] = isset($orderInfo[$value['commodity_id']][$value['unit']]) ? $orderInfo[$value['commodity_id']][$value['unit']] : 0;//订单数量
            $value['early_stock'] = 0;//期初库存
            $value['early_price'] = 0;//期初金额
            $value['in_count'] = isset($stockInInfo[$value['commodity_id']][$value['unit']]['count']) ? $stockInInfo[$value['commodity_id']][$value['unit']]['count'] : 0;//入库数量
            $value['in_price'] = isset($stockInInfo[$value['commodity_id']][$value['unit']]['price']) ? $stockInInfo[$value['commodity_id']][$value['unit']]['price'] : 0.00;//入库金额
            $value['out_count'] = isset($stockOutInfo[$value['commodity_id']][$value['unit']]['count']) ? $stockOutInfo[$value['commodity_id']][$value['unit']]['count'] : 0;//出库数量
            $value['out_price'] = isset($stockOutInfo[$value['commodity_id']][$value['unit']]['price']) ? $stockOutInfo[$value['commodity_id']][$value['unit']]['price'] : 0;//出库金额
            $value['overflow_count'] = isset($lossOverflowInfo[$value['commodity_id']][$value['unit']][1]['count']) ? $lossOverflowInfo[$value['commodity_id']][$value['unit']][1]['count'] : 0;//报溢数量
            $value['overflow_price'] = isset($lossOverflowInfo[$value['commodity_id']][$value['unit']][1]['price']) ? $lossOverflowInfo[$value['commodity_id']][$value['unit']][1]['price'] : 0.00;//报溢金额
            $value['loss_count'] = isset($lossOverflowInfo[$value['commodity_id']][$value['unit']][2]['count']) ? $lossOverflowInfo[$value['commodity_id']][$value['unit']][2]['count'] : 0;;//报损数量
            $value['loss_price'] = isset($lossOverflowInfo[$value['commodity_id']][$value['unit']][2]['price']) ? $lossOverflowInfo[$value['commodity_id']][$value['unit']][2]['price'] : 0.00;//报损金额
        }
        return [
            'total' => $count,
            'list' => $data,
        ];
    }


    /**
     * 获取商品订购数据
     * @return array
     */
    public function getOrderInfo()
    {
        $query = OrderDetail::find();
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
        $list = $query->select('commodity_id,sum(num) as total_count,unit')
            ->asArray()
            ->groupBy(['commodity_id', 'unit'])
            ->all();
        $data = [];
        foreach ($list as $k => &$v) {
            $data[$v['commodity_id']][$v['unit']] = $v['total_count'];
        }
        return $data;
    }

    /**
     * 获取商品入库信息
     * @return array
     */
    public function getStockInInfo()
    {
        $query = StockInDetail::find();
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
            ->select('commodity_id,sum(num) as total_count,unit,sum(price) as total_price')
            ->asArray()
            ->groupBy(['commodity_id', 'unit'])
            ->all();
        $data = [];
        foreach ($list as $k => &$v) {
            $data[$v['commodity_id']][$v['unit']]['count'] = $v['total_count'];
            $data[$v['commodity_id']][$v['unit']]['price'] = $v['total_price'];
        }
        return $data;
    }

    /**
     * 获取商品出库信息
     * @return array
     */
    public function getStockOutInfo()
    {
        $query = StockOutDetail::find();
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
            ->select('commodity_id,sum(num) as total_count,unit,sum(price) as total_price')
            ->asArray()
            ->groupBy(['commodity_id', 'unit'])
            ->all();
        $data = [];
        foreach ($list as $k => &$v) {
            $data[$v['commodity_id']][$v['unit']]['count'] = $v['total_count'];
            $data[$v['commodity_id']][$v['unit']]['price'] = $v['total_price'];
        }
        return $data;
    }

    /**
     * 获取商品报损报溢信息
     * @return array
     */
    public function getOverflowInfo()
    {
        $query = StockLossOverflowDetail::find();
        if ($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'bn_stock_loss_overflow_detail.create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=', 'bn_stock_loss_overflow_detail.create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        $list = $query
            ->select('commodity_id,sum(bn_stock_loss_overflow_detail.num) as total_count,unit,sum(bn_stock_loss_overflow_detail.price) as total_price,type')
            ->leftJoin('bn_stock_loss_overflow', 'bn_stock_loss_overflow.id = bn_stock_loss_overflow_detail.loss_over_id')
            ->asArray()
            ->groupBy(['commodity_id', 'unit', 'type'])
            ->all();
        $data = [];
        foreach ($list as $k => &$v) {
            $data[$v['commodity_id']][$v['unit']][$v['type']]['count'] = $v['total_count'];
            $data[$v['commodity_id']][$v['unit']][$v['type']]['price'] = $v['total_price'];
        }
        return $data;
    }

    /**
     * 获取商品价格浮动数据
     * @param $commodityId
     * @return array
     */
    public function getCommodityPriceFluctuation($commodityId = 0,$unit)
    {
        if (!$commodityId || !$this->create_time) {
            return [];
        }
        $date = explode(' - ',$this->create_time);
        $dateList = $this->getDateFromRange($date[0],$date[1]);
        //采购价
        $model = PurchaseDetail::find();
        $purchaseQuery = $model
            ->select('FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,min(price) as purchase_price')
            ->where(['commodity_id' => $commodityId,'unit'=>$unit])
            ->groupBy('date')
            ->asArray()
            ->all();
        $purchasePrice = array_column($purchaseQuery,'purchase_price','date');
        //销售价
        $orderDetail = OrderDetail::find();
        $userQuery = $orderDetail
            ->select('FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,max(price) as user_price')
            ->groupBy('date')
            ->where(['commodity_id' => $commodityId,'unit'=>$unit])
            ->asArray()
            ->all();
        $userPrice = array_column($userQuery,'user_price','date');
        $list = [];
        foreach($dateList as $k=>$v){
            $list[$k]['date'] = $v;
            $list[$k]['purchase_price'] = isset($purchasePrice[$v])?$purchasePrice[$v]:0;
            $list[$k]['user_price'] = isset($userPrice[$v])?$userPrice[$v]:0;
        }
        return ['list' => $list];
    }


    /**
     * 获取日期段所有日期
     * @param $sTimestamp : 开始日期
     * @param $eTimestamp : 结束日期
     * @return mixed
     */

    function getDateFromRange($startDate, $endDate)
    {
        $sTimestamp = strtotime($startDate);
        $eTimestamp = strtotime($endDate);
        // 计算日期段内有多少天
        $days = ($eTimestamp - $sTimestamp) / 86400 + 1;
        // 保存每天日期
        $date = array();
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $sTimestamp + (86400 * $i));
        }
        return $date;
    }


    /**
     * 获取格式化分类数据
     * @return array
     */
    public function getCategoryList()
    {
        $commodityCategory = CommodityCategory::find()
            ->select('id,name,pid')
            ->asArray()
            ->all();//获取所有分类
        $categoryIndex = array_column($commodityCategory, 'name', 'id'); //生成商分类Map
        $categoryIndexList = array();
        //格式化数组
        foreach ($commodityCategory as $k => $v) {
            $categoryIndexList[$v['id']]['id'] = $v['id'];
            $categoryIndexList[$v['id']]['name'] = $v['name'];
            $categoryIndexList[$v['id']]['parent_name'] = ($v['pid'] == 0) ? '顶级分类' : $categoryIndex[$v['pid']];
        }
        return $categoryIndexList;
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
