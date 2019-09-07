<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityProfileDetail;
use app\models\DeliveryLine;
use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use app\models\OrderRefund;
use common\models\User;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Expression;

class OrderForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $delivery_date;
    public $user;
    public $user_id;
    public $order_ids;
    public $type_id;
    public $source;
    public $achieve_date;
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
            [['status',], 'default', 'value' => 1,],
            [['source',], 'default', 'value' => ''],
            [['is_pay',], 'default', 'value' => ''],
            [['order_ids',], 'default', 'value' => ''],
            [['user_id',], 'default', 'value' => ''],
            [['type_id',], 'default', 'value' => ''],
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
        $query = Order::find();
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

        if ($this->order_ids) {
            $query->andWhere(['in', 'id', explode(',', $this->order_ids)]);
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
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 获取退款退货列表
     * @return array
     */
    public function getRefund()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = OrderRefund::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'user_name', $this->keyword],
                ['like', 'refund_no', $this->keyword],
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
            $v['create_time'] = date("Y-m-d H:i:s", $v['create_time']);
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
            $model = Order::findOne($order_id);
            $model->status = $status;
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
        if ($id) {
            $order = Order::findOne($post['id']);
        } else {
            $order = new Order();
            $order->order_no = $this->getOrderNo();
        }
        $order->attributes = $post;//属性赋值
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
        $model->pay_price = 0;
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = $this->pay_text['N'];
        $model->source = $this->source;
        $model->source_txt = isset($this->source_text[$this->source]) ? $this->source_text[$this->source] : '小程序';
        $lineInfo = $this->getLineInfo($model->user_id);
        if ($lineInfo) {
            $model->driver_id = $lineInfo['driver_id'];
            $model->driver_name = $lineInfo['driver_name'];
            $model->line_id = $lineInfo['id'];
            $model->line_name = $lineInfo['name'];
        }
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
            if (isset($v['price'])) {
                $total += $v['price'] * $v['num'];
            }

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
            $attribute['commodity_name'] = isset($attribute['name']) ? $attribute['name'] : (isset($attribute['commodity_name']) ? $attribute['commodity_name'] : '');
            $_model = clone $orderDetail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'price' => $attribute['price'],
                    'notice' => $attribute['notice'],
                    'num' => $attribute['num'],
                    'pic' => $attribute['pic'],
                    'channel_type' => isset($attribute['channel_type']) ? $attribute['channel_type'] : 0,
                    'total_price' => $attribute['num'] * $attribute['price'],
                    'remark' => isset($attribute['remark']) ? $attribute['remark'] : '',
                    'is_delete' => 0,
                    'update_at' => time(),
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->delivery_date = $order->delivery_date;
            $_model->order_id = $order->id;
            $_model->base_unit = $_model->unit;
            $_model->base_self_ratio = 0;
            $_model->is_basics_unit = 1;
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
        $query = Order::find()
            ->with([
                'details' => function ($query) {
                    $query->select('*,commodity_name as name')
                        ->andWhere('is_delete=0');
                },
            ])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        if ($query) {
            $query['actual_price'] = $query['reduction_price'] = 0;
            //$query['need_pay'] = $query['price'] - $query['is_pay'];
            $query['settlement_type'] = $this->settlement_type[1];
            //$query['actual_price'] = $query['need_pay'];
            foreach ($query['details'] as &$v) {
                $v['total_actual_price'] = $v['price'] * $v['actual_num'];
            }
        }
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
     * 获取线路信息
     * @param $driverId
     * @return array
     */
    public function getLineInfo($user_id)
    {
        $lineInfo = User::findOne($user_id);
        $query = DeliveryLine::findOne($lineInfo['line_id'])->toArray();
        return $query;
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
        $model = Order::find();
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
        if (!$id) {
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
        $queryProduct = $model->select('commodity_id as id,commodity_name as name,sum(total_price) as total_price,unit,sum(num) AS num')
            ->asArray()
            ->groupby('commodity_id,unit')
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
    public function getOrderCommodityDetail($user_id, $id, $unit)
    {
        if (!$user_id) {
            return [];
        }
        $user = User::findOne($user_id);
        $model = $user->getOrderItems();
        $queryProduct = $model->select('bn_order.order_no,bn_order.delivery_date,commodity_id,commodity_name as name,total_price,unit,num,refund_num')
            ->where(['commodity_id' => $id, 'unit' => $unit])
            ->join('LEFT JOIN', 'bn_order', 'bn_order_detail.order_id=bn_order.id')
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
    public function getPrintData($id)
    {
        $model = Order::find()->where(['id' => $id])->one();
        $item = $model->getDetails()->select('commodity_name as name,unit,num as amount_with_unit,parent_type_name as category_name,type_name as category_name2,unit as summary,actual_num as actual_amount_with_unitsell,price as unit_price,(actual_num*price) as row_money')->asArray()->all();
        $order = $model->toArray();
        $order['item'] = $item;
        return ['order' => $order, 'shop' => [], 'user' => []];
    }


    /**
     * 批量更新商品价格
     * @return bool
     */
    public function orderAllAudit()
    {
        $post = \Yii::$app->request->post();
        $price = $post['price'];
        $commodity_id = $post['commodity_id'];
        $data_id = $post['data_id'];
        $date = $post['date'];
        $type_id = $post['type_id'];
        $type_name = $post['type_name'];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->updateOrderPrice($commodity_id, $type_name, $type_id, $price, $date);
            if ($post['sync']) {
                $this->updateCommodityProfile($data_id, $price);
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 更新关联订单价格数据
     * @param $commodity_id
     * @param $type_name
     * @param $type_id
     * @param $price
     * @param $date
     * @throws Exception
     */
    public function updateOrderPrice($commodity_id, $type_name, $type_id, $price, $date)
    {
        //更新订单商品价格
        $where = ['commodity_id' => $commodity_id, 'delivery_date' => $date, 'c_type' => $type_id, 'unit' => $type_name];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            OrderDetail::updateAll(['price' => $price, 'total_price' => new Expression('num * ' . $price)], $where);
            //更新订单总价
            $query = OrderDetail::find()->select('order_id')->where($where)->asArray()->groupBy('order_id')->all();
            $this->sumOrderCommodityPrice($query);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new Exception('价格更新失败');
        }

    }

    /**
     * 计算订单商品总价
     * @param $query
     */
    public function sumOrderCommodityPrice($query)
    {
        if ($query) {
            foreach ($query as $k => $v) {
                $orderId = $v['order_id'];
                $OrderDetailTotalPrice = OrderDetail::find()->select('sum(total_price) as c_total_price')->where(['order_id' => $orderId, 'is_delete' => 0])->asArray()->one();
                $order = Order::findOne($orderId);
                $order->price = $OrderDetailTotalPrice['c_total_price'];
                $order->save();
            }
        }

    }

    /**
     * 同步更新商品价格
     * @param $id
     * @param $price
     */

    public function updateCommodityProfile($id, $price)
    {
        $model = CommodityProfileDetail::findOne($id);
        $model->price = $price;
        $model->save();
    }

    // 按客户出库的数据
    public function outOfStockData($filterProperty)
    {
        $query = Order::find()
            ->select(['bn_order.user_id, bn_order.user_name, bn_order.delivery_date, bn_order.nick_name, bn_order.receive_name, bn_order.status, bn_order.receive_tel, bn_order.address_detail, bn_order.line_id, bn_order.line_name, round(sum(bn_order_detail.price * actual_num), 2) as sendPrice', ' sum(if(bn_order_detail.is_sorted = 1, 1, 0)) as sortedNum', 'count(bn_order_detail.id) as totalNum'])
            ->leftJoin('bn_order_detail', 'bn_order_detail.order_id = bn_order.id')
            ->groupBy('user_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty, true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order.delivery_date' => $delivery_date]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['or', ['like', 'bn_order.receive_name', "%$searchText%", false], ['like', 'bn_order.receive_tel', "%$searchText%", false]]);
            }
            $orderStatus = isset($json['orderStatus']) ? $json['orderStatus'] : null;
            if ($orderStatus != null) {
                if ($orderStatus == 0) {
                    // 未发货
                    $query->andWhere(['status' => 1]);
                } else {
                    // 已发货
                    $query->andWhere(['>=', 'status', 2]);
                }
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('user_id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return [
            'total' => $count,
            'list' => $list
        ];
    }

    // 按订单出库的数据
    public function outOfStockByOrderData($filterProperty)
    {
        $query = Order::find()
            ->select(['bn_order.id, bn_order.order_no, bn_order.user_id, bn_order.user_name, bn_order.delivery_date, bn_order.delivery_time_detail, bn_order.nick_name, bn_order.receive_name, bn_order.status, bn_order.receive_tel, bn_order.address_detail, bn_order.line_id, bn_order.line_name, round(sum(bn_order_detail.price * actual_num), 2) as sendPrice', ' sum(if(bn_order_detail.is_sorted = 1, 1, 0)) as sortedNum', 'count(bn_order_detail.id) as totalNum'])
            ->leftJoin('bn_order_detail', 'bn_order_detail.order_id = bn_order.id')
            ->groupBy('id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty, true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order.delivery_date' => $delivery_date]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['or', ['like', 'bn_order.receive_name', "%$searchText%", false], ['like', 'bn_order.receive_tel', "%$searchText%", false]]);
            }
            $orderStatus = isset($json['orderStatus']) ? $json['orderStatus'] : null;
            if ($orderStatus != null) {
                if ($orderStatus == 0) {
                    // 未发货
                    $query->andWhere(['status' => 1]);
                } else {
                    // 已发货
                    $query->andWhere(['>=', 'status', 2]);
                }
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('user_id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return [
            'total' => $count,
            'list' => $list
        ];
    }


    // 客户出库数据详情
    public function outOfStockDataView($userId, $delivery_date)
    {
        $list = OrderDetail::find()->asArray()
            ->select('bn_order_detail.*')
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->where(['bn_order.delivery_date' => $delivery_date])
            ->andWhere(['bn_order.user_id' => $userId])
            ->all();

        return [
            'total' => 0,
            'list' => $list
        ];
    }

    // 客户出库数据详情
    public function outOfStockByOrderDataView($orderId)
    {
        $list = OrderDetail::find()->asArray()
            ->andWhere(['order_id' => $orderId])
            ->all();

        return [
            'total' => 0,
            'list' => $list
        ];
    }

    // 出库按照日期跟客户
    public function sendOutByDateAndUserId($date, $userId)
    {
        $orderData = Order::find()->asArray()
            ->where(['user_id' => $userId])
            ->andWhere(['delivery_date' => $date])
            ->andWhere(['status' => 1])
            ->all();
        $orderDetailUnSort = [];
        $isSendOut = true;
        foreach ($orderData as $item) {
            // 查询未分拣的数据
            $orderDetailData = OrderDetail::find()->asArray()
                ->where(['order_id' => $item['id']])
                ->andWhere(['is_sorted' => 0])
                ->all();
            if (count($orderDetailData) > 0) {
                $isSendOut = false;
                $orderDetailUnSort = array_merge($orderDetailUnSort, $orderDetailData);
            }
        }
        if ($isSendOut) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                // 生成出库单如果未付款生成对账单，已付款通过余额处理多退少补的问题
                $stockOut = new StockOutForm();
                $stockOut->saveByOrderList($orderData);
                // 出库
                Order::updateAll(['status' => 2, 'status_text' => $this->status_text[2]], ['user_id' => $userId, 'delivery_date' => $date]);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw new \yii\base\Exception('生成出库单失败');
            }
        }
        return ['isSendOut' => $isSendOut, 'listData' => $orderDetailUnSort];
    }

    // 出库按照订单id
    public function sendOutByOrderId($orderId)
    {
        $orderDetailUnSort = [];
        $isSendOut = true;
        // 查询未分拣的数据
        $orderDetailData = OrderDetail::find()->asArray()
            ->where(['order_id' => $orderId])
            ->andWhere(['is_sorted' => 0])
            ->all();

        if (count($orderDetailData) > 0) {
            $isSendOut = false;

            $orderDetailUnSort = array_merge($orderDetailUnSort, $orderDetailData);
        }

        if ($isSendOut) {

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                // 生成出库单
                $stockOut = new StockOutForm();
                $stockOut->saveByOrderList(Order::find()->asArray()->where(['id' => $orderId])->andWhere(['status' => 1])->all());
                // 出库
                Order::updateAll(['status' => 2, 'status_text' => $this->status_text[2]], ['id' => $orderId]);

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw new \yii\base\Exception('生成出库单失败');
            }
        }

        return ['isSendOut' => $isSendOut, 'listData' => $orderDetailUnSort];
    }

    // 出库按照日期
    public function sendOutByDate($date)
    {
        $orderData = Order::find()->asArray()
            ->andWhere(['delivery_date' => $date])
            ->andWhere(['status' => 1])
            ->all();

        $orderDetailUnSort = [];
        $isSendOut = true;
        foreach ($orderData as $item) {
            // 查询未分拣的数据
            $orderDetailData = OrderDetail::find()->asArray()
                ->where(['order_id' => $item['id']])
                ->andWhere(['is_sorted' => 0])
                ->all();

            if (count($orderDetailData) > 0) {
                $isSendOut = false;

                $orderDetailUnSort = array_merge($orderDetailUnSort, $orderDetailData);
            }
        }

        if ($isSendOut) {

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                // 生成出库单
                $stockOut = new StockOutForm();
                $stockOut->saveByOrderList($orderData);
                // 出库
                Order::updateAll(['status' => 2, 'status_text' => $this->status_text[2]], ['delivery_date' => $date]);

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw new \yii\base\Exception('生成出库单失败');
            }
        }
        return ['isSendOut' => $isSendOut, 'listData' => $orderDetailUnSort];
    }


    /**
     * 获取批量核价数据
     * @return array
     */
    public function getAuditAllData()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = OrderDetail::find();
        if ($this->delivery_date) {
            $query->andwhere(['bn_order_detail.delivery_date' => $this->delivery_date]);
        } else {
            $query->andwhere(['bn_order_detail.delivery_date' => date("Y-m-d", strtotime("+1 day"))]);
        }
        if ($this->type_id) {
            $query->andwhere(['bn_order_detail.type_first_tier_id' => $this->type_id]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'commodity_name', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->groupBy('bn_order_detail.commodity_id')
            ->all();
        $ids = array_column($list, 'commodity_id');
        $priceInfo = $this->getCommodityPriceInfo($ids);
        foreach ($list as &$v) {
            $v['priceInfo'] = isset($priceInfo[$v['commodity_id']]) ? $priceInfo[$v['commodity_id']] : [];
            $v['in_price'] = isset($priceInfo['in_prices'][$v['commodity_id']][$v['unit']]['in_price']) ? $priceInfo['in_prices'][$v['commodity_id']][$v['unit']]['in_price'] : 0;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 获取商品客户类型价格信息
     * @param array $ids
     * @return array
     */
    public function getCommodityPriceInfo($ids = [])
    {
        $query = CommodityProfileDetail::find()
            ->select('bn_commodity_profile_detail.*,bn_commodity_profile.name,bn_commodity_profile.in_price,base_self_ratio')
            ->leftJoin('bn_commodity_profile', 'bn_commodity_profile.id = bn_commodity_profile_detail.commodity_profile_id')
            ->where(['in', 'bn_commodity_profile_detail.commodity_id', $ids])
            ->asArray()
            ->all();
        $data = $inPrice = [];
        foreach ($query as $k => $v) {
            $inPrice[$v['commodity_id']][$v['name']]['in_price'] = $v['in_price'];
            $data[$v['commodity_id']][$k]['id'] = $v['id'];
            $data[$v['commodity_id']][$k]['price'] = $v['price'];
            $data[$v['commodity_id']][$k]['name'] = $v['name'];
            $data[$v['commodity_id']][$k]['base_self_ratio'] = $v['base_self_ratio'];
            $data[$v['commodity_id']][$k]['type_id'] = $v['type_id'];
        }
        $data['in_prices'] = $inPrice;
        return $data;
    }


    /**
     * 获取用户订单商品详情
     */
    public function getOrderDetails()
    {
        $post = Yii::$app->request->get();
        $this->attributes = json_decode($post['field'], true);
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
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
        if ($this->type_id) {
            $query->andwhere(['bn_order_detail.type_first_tier_id' => $this->type_id]);
        }

        $userInfo = [];
        if ($this->user_id) {
            $query->andwhere(['bn_order.user_id' => $this->user_id]);
            $userInfo = User::findOne($this->user_id);
        }

        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'bn_order_detail.commodity_name', $this->keyword],
            ]);
        }
        $list = $query
            ->select('bn_order_detail.commodity_name,bn_order_detail.parent_type_name,bn_order_detail.type_name,bn_order_detail.unit,sum(bn_order_detail.total_price) as total_price,sum(bn_order_detail.num) as total_num,bn_order_detail.remark,bn_order.user_id')
            ->leftJoin('bn_order', 'bn_order.id=bn_order_detail.order_id')
            ->asArray()
            ->groupBy('bn_order_detail.commodity_id,bn_order_detail.unit')
            ->all();
        return ['list' => $list, 'sql' => $this->getLastSql($query), 'userInfo' => $userInfo];
    }


    /**
     * 导出采购单
     * @param $id
     */
    public function exportUserOrderDetail()
    {
        $orderDetail = $this->getOrderDetails();
        $excel = new PHPExcel();
        $excel->getProperties()
            ->setTitle("客户订单商品明细")
            ->setSubject("客户订单商品明细");
        //合并单元格
        $excel->getActiveSheet()->mergeCells('A1:G1');
        //设置样式
        $excel->getDefaultStyle()->getFont()->setName('微软雅黑');
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);         //第一行字体大小
        // 设置行高度
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23); //设置默认行高
        $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);    //第一行行高
        // 设置垂直居中
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $excel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格颜色
        $excel->getActiveSheet()->getStyle('A1:G1')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $excel->getActiveSheet()->getStyle('A2:G2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        //设置表头
        $userInfo = isset($orderDetail['userInfo']['nickname']) ? $orderDetail['userInfo']['nickname'] : '';
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', $userInfo . '订单发货记录' . $this->create_time)
            ->setCellValue('A2', '商品名称')
            ->setCellValue('B2', '一级分类')
            ->setCellValue('C2', '二级分类')
            ->setCellValue('D2', '描述')
            ->setCellValue('E2', '数量')
            ->setCellValue('F2', '单位')
            ->setCellValue('G2', '金额小计');
        //赋值
        $row = 3;
        foreach ($orderDetail['list'] as $v) {
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $v['commodity_name'])
                ->setCellValue('B' . $row, $v['parent_type_name'])
                ->setCellValue('C' . $row, $v['type_name'])
                ->setCellValue('D' . $row, $v['remark'])
                ->setCellValue('E' . $row, $v['unit'])
                ->setCellValue('F' . $row, $v['total_num'])
                ->setCellValue('G' . $row, $v['total_price']);
            ++$row;
        }

        //设置单元格宽度
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);

        //设置标题
        $excel->getActiveSheet()->setTitle('商品明细');
        $excel->setActiveSheetIndex(0);

        //加边框
        $styleThinBlackBorderOutline = array('borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),),);
        $excel->getActiveSheet()->getStyle('A1:G' . ($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        //外边框加粗
        $styleThinBlackBorderOutline = array('borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK),),);
        $excel->getActiveSheet()->getStyle('A1:G' . ($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        // 设置输出
        $tableName = $userInfo . '订单发货记录' . $this->create_time;
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $tableName . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        ob_end_flush();
        ob_clean();
    }

    /**
     * 导出订单列表
     */
    public function exportOrderList(){
        $post = Yii::$app->request->post('ex_field');
        $column = [
            'order_no'=>'订单编号',
            'nick_name'=>'客户名称',
            'price'=>'下单金额',
            'delivery_date'=>'发货日期',
            'status'=>'订单状态',
            'is_pay_text'=>'付款状态',
            'source_txt'=>'订单来源',
            'line_name'=>'线路名称',
            'driver_name'=>'司机名称',
            'address_detail'=>'收货地址',
            'receive_name'=>'收货人',
        ];

        $orderList = $this->search();
        $excel = new PHPExcel();
        $excel->getProperties()
            ->setTitle("订单导出")
            ->setSubject("订单导出");
        //合并单元格
        $excel->getActiveSheet()->mergeCells('A1:K1');
        //设置样式
        $excel->getDefaultStyle()->getFont()->setName('微软雅黑');
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);      //第一行是否加粗
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);         //第一行字体大小
        // 设置行高度
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(23); //设置默认行高
        $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);    //第一行行高
        // 设置垂直居中
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $excel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置单元格颜色
        $excel->getActiveSheet()->getStyle('A1:K1')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        $excel->getActiveSheet()->getStyle('A2:K2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E0E0E0')));
        //设置表头
        $excel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单导出' . $this->create_time)
            ->setCellValue('A2', '订单编号' . $this->create_time)
            ->setCellValue('B2', '客户名称')
            ->setCellValue('C2', '下单金额')
            ->setCellValue('D2', '发货日期')
            ->setCellValue('E2', '订单状态')
            ->setCellValue('F2', '付款状态')
            ->setCellValue('G2', '订单来源')
            ->setCellValue('H2', '线路名称')
            ->setCellValue('I2', '司机名称')
            ->setCellValue('J2', '收货地址')
            ->setCellValue('K2', '收货人');
        //赋值
        $row = 3;
        foreach ($orderList['list'] as $v) {
            $excel->setActiveSheetIndex(0)
                ->setCellValue('A' . $row, $v['order_no'])
                ->setCellValue('B' . $row, $v['nick_name'])
                ->setCellValue('C' . $row, $v['price'])
                ->setCellValue('D' . $row, $v['delivery_date'])
                ->setCellValue('E' . $row, $v['status'])
                ->setCellValue('F' . $row, $v['is_pay_text'])
                ->setCellValue('G' . $row, $v['source_txt'])
                ->setCellValue('H' . $row, $v['line_name'])
                ->setCellValue('I' . $row, $v['driver_name'])
                ->setCellValue('J' . $row, $v['address_detail'])
                ->setCellValue('K' . $row, $v['receive_name']);
            ++$row;
        }

        //设置单元格宽度
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

        //设置标题
        $excel->getActiveSheet()->setTitle('商品明细');
        $excel->setActiveSheetIndex(0);

        //加边框
        $styleThinBlackBorderOutline = array('borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),),);
        $excel->getActiveSheet()->getStyle('A1:K' . ($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        //外边框加粗
        $styleThinBlackBorderOutline = array('borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK),),);
        $excel->getActiveSheet()->getStyle('A1:K' . ($row + 2))->applyFromArray($styleThinBlackBorderOutline);

        // 设置输出
        $tableName ='订单导出记录' . $this->create_time;
        $fileName = date('YmdHis',time()).'.xls';
        $path = Yii::$app->basePath.'/web/uploads/'.$fileName;
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save($path);
        return ['fileName'=>Yii::$app->urlManager->createAbsoluteUrl('uploads').'/'.$fileName];
    }
}
