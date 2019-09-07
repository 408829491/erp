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

class CusOrderForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $delivery_date;
    public $user;
    public $source;
    public $create_time;
    public $status_text = [
        1 => '待付款',
        2 => '待发货',
        3 => '待收货',
        4 => '待评价',
        5 => '退款',
        6 => '已完成',
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

        $storeId = \Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => \Yii::$app->user->identity['store_id']]);
        }

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
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 更新订单状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updateOrderStatus($order_id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = CusOrder::findOne($order_id);
            $model->status = 4;
            $model->status_text = $this->status_text[$status];
            if ($model->save()) {
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return $model->getErrors();
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


    /**
     * 保存订单
     * @return array
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $order = ($id) ? CusOrder::findOne($post['id']) : new Order();
        $order->attributes = $post;//属性赋值
        $order->order_no = $this->getOrderNo();
        $order = $this->initOrderData($order);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $order->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$order->validate() || !$order->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $order->getErrors()];
        }
        $order->price = $this->getSummary($order->commodity_list);
        if ($res = $order->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($order->commodity_list, $order, $id);
            if ($res_detail['code'] == 400) {
                $t->rollBack();
                return ['code' => 400, 'msg' => '商品数据保存失败', 'data' => $res_detail['data']];
            }
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $t->commit();//提交事务
        return $res;
    }


    /**
     * 初始化订单数据
     * @param $model
     * @return mixed
     */
    private function initOrderData($model)
    {
        $status = 1;
        $this->source = 3;
        $model->order_no = $this->getOrderNo();
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = $this->pay_text['N'];
        $model->source = $this->source;
        $model->source_txt = isset($this->source_text[$this->source]) ? $this->source_text[$this->source] : '小程序';
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算订单总额
     * @param array $commodity_list
     * @return mixed
     */
    private function getSummary($commodity_list = [])
    {
        $total = 0;
        foreach ($commodity_list as $v) {
            $total += $v['price'] * $v['num'];
        }
        return $total;
    }

    /**
     * 插入订单商品数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $order, $id = 0)
    {
        $orderDetail = new OrderDetail();
        $b = \Yii::$app->db->beginTransaction();
        $orderDetail->updateAll(['is_delete' => 1], ['order_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $orderDetail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'price' => $attribute['price'],
                    'notice' => $attribute['notice'],
                    'num' => $attribute['num'],
                    'pic' => $attribute['pic'],
                    'total_price' => $attribute['num'] * $attribute['price'],
                    'remark' => $attribute['remark'],
                    'is_delete' => 0,
                    'update_at' => time(),
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->order_id = $order->id;
            $_model->total_price = $_model->price * $_model->num;
            if (!$_model->save()) {//创建新记录
                $b->rollBack();
                return [
                    'code' => 400,
                    'msg' => '订单提交失败，请稍后再重试',
                    'data' => $_model->getErrors()
                ];
            }
        }
        $b->commit();
        return [
            'code' => 200,
            'msg' => 'ok',
            'data' => []
        ];
    }


    /**
     * 获取订单详情
     * @param $id
     * @return array
     */
    public function getData($id)
    {
        $query = CusOrder::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
        if ($query) {
            $query['actual_price'] = $query['reduction_price'] = 0;
            $query['need_pay'] = $query['price'] - $query['is_pay'];
            $query['settlement_type'] = $this->settlement_type[1];
            $query['actual_price'] = $query['need_pay'];
        }
        $detail = CusOrderDetail::find()
            ->where(['order_id'=>$query['id']])
            ->asArray()
            ->all();
        $query['details']=$detail;
        return $query;
    }

    /**
     * 生成订单号
     * @return null|string
     */
    public function getOrderNo()
    {
        $order_no = null;
        while (true) {
            $order_no = 'DD' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_order_no = Order::find()->where(['order_no' => $order_no])->exists();
            if (!$exist_order_no) {
                break;
            }
        }
        return $order_no;
    }



    /**
     * 客户下单统计
     * @return mixed
     * @throws ErrorException
     */
    public function getUserOrderData()
    {
        $this->attributes = Yii::$app->request->get();
        $model = Order::find();
        if ($this->keyword) {
            $model->andwhere([
                'or',
                ['like', 'nick_name', $this->keyword],
            ]);
        }
        if ($this->create_time) {
            $model->andwhere([
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
        $count = $model->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $model->select('user_id,user_name,nick_name,max(create_time) as create_time,count(id) as count,sum(price) as money,sum(pay_price) as pay_money,address_detail');
        $list = $model
            ->groupBy('user_id')
            ->offset($pagination->offset)
            ->asArray();
        $count = $model->count();
        $list = $model->limit($pagination->pageSize)
            ->all();
        array_walk($list, function (&$data) {
            $data['create_time'] = date('Y-m-d H:i:s', $data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($model)
        ];
    }


    /**
     * 获取客户下单列表数据
     * @param $userId
     * @return array
     */
    public function getUserOrderListData($userId)
    {
        $this->attributes = Yii::$app->request->get();
        $model = CusOrder::find();
        $model->where(['user_id' => $userId]);
        $count = $model->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $model
            ->offset($pagination->offset)
            ->asArray()
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list, function (&$data) {
            $data['create_time'] = date('Y-m-d H:i:s', $data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($model)
        ];
    }

    /**
     * 获取订单商品汇总数据
     * @return mixed
     */
    public function getOrderCommodityList($id)
    {
        if(!$id){
            return [];
        }
        $this->attributes = Yii::$app->request->get();
        $user = User::findOne($id);
        $model = $user->getOrderItems();
        if ($this->keyword) {
            $model->andWhere([
                'or',
                ['like', 'commodity_name', $this->keyword],
            ]);
        }
        $queryProduct = $model->select('commodity_id as id,commodity_name as name,sum(total_price) as total_price,unit,sum(num) AS num')
            ->asArray()
            ->groupby('commodity_id')
            ->all();
        foreach ($queryProduct as &$v) {
            $v['user_id'] = $id;
        }
        return [
            'list' => $queryProduct,
            'sql' => $this->getLastSql($model)
        ];
    }

    /**
     * 获取订单商品汇总详情数据
     * @return mixed
     */
    public function getOrderCommodityDetail($user_id,$id)
    {
        if(!$user_id){
            return [];
        }
        $user = User::findOne($user_id);
        $model = $user->getOrderItems();
        $queryProduct = $model->select('bn_order.order_no,bn_order.delivery_date,commodity_id,commodity_name as name,total_price,unit,num,refund_num')
            ->where(['commodity_id'=>$id])
            ->join('LEFT JOIN','bn_order','bn_order_detail.order_id=bn_order.id')
            ->asArray()
            ->limit(10)
            ->all();
        return [
            'list' => $queryProduct,
            'sql' => $this->getLastSql($model)
        ];
    }

    /**
     * 获取打印数据
     * @param $id
     * @return array
     */
    public function getPrintData($id){
        $model = CusOrder::find()->where(['id'=>$id])->one();
        $item = $model->getDetails()->select('commodity_name as name,unit,num as amount_with_unit,parent_type_name as category_name,type_name as category_name2,unit as summary,actual_num as actual_amount_with_unitsell,price as unit_price,(actual_num*price) as row_money')->asArray()->all();
        $order = $model->toArray();
        $order['item'] = $item;
        return ['order'=>$order,'shop'=>[],'user'=>[]];
    }
}
