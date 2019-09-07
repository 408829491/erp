<?php

namespace app\models\form;

use app\models\Admin;
use app\models\Commodity;
use app\models\CusDeliveryman;
use app\models\CusDiscountCouponGetRecord;
use app\models\CusOrder;
use app\models\CusOrderDetail;
use app\models\Order;
use common\models\UserCus;
use common\models\UserDelivery;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;

class CusOrderForm extends \yii\base\Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $pay_way;
    public $delivery_date;
    public $create_time;
    public $user;
    public $source;
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

    public $pay_way_text = [
        1 => '余额付款',
        2 => '微信付款',
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['is_pay',], 'default', 'value' => ''],
            [['pay_way',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
        ];
    }

    public static function tableName()
    {
        return '{{%cus_order}}';
    }

    /**
     * 查询订单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return [];
        }
        $query = CusOrder::find();
        $query->where(['user_id' => $this->user->id]);
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
                ['like', 'driver_name', $this->keyword],
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
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query
            ->with('details')
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->asArray()
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as &$v) {
            $v['num'] = count($v['details']);
            $v['create_time'] = $this->dateFormat($v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 获取订单详情
     * @param $order_id
     * @return mixed
     */
    public function getOrderDetail($order_id)
    {
        $model = CusOrder::find();
        $query = $model->select('bn_cus_order.*,bn_cus_store.name,bn_cus_store.address as store_address,lat as store_lat,lng as store_lng,relation_phone as store_relation_phone,relation_people as store_relation_people')->leftJoin('bn_cus_store', 'bn_cus_order.store_id=bn_cus_store.id')->where(['bn_cus_order.id' => $order_id])->joinWith('details')->asArray()->one();
        if (is_array($query))
            $query['create_time'] = $this->dateFormat($query['create_time']);
        return $query;
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
            $model->status = $status;
            $model->status_text = $this->status_text[$status];
            if($status === 3)
            {
                $model->achieve_date = time();
            }
            if ($model->save()) {
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return false;
    }

    /**
     * 配送异常状态更新
     * @param $order_id
     * @param $status
     * @return bool
     * @throws ErrorException
     */
    public function updateOrderExceptionStatus($order_id, $status)
    {
        $model = CusOrder::findOne($order_id);
        $model->exception_status = $status;
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * 更新订单状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updatePayStatus($order_id, $status)
    {
        if (in_array($status, array_keys($this->pay_text))) {
            $model = CusOrder::findOne($order_id);
            $model->is_pay = $status;
            $model->is_pay_text = $this->pay_text[$status];
            if ($model->save()) {
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return false;
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval, $timestamp = 1)
    {
        $dates = explode(' - ', $date_interval);
        if (count($dates) == 1) {
            return date("Y-m-d H:i:s", $date_interval);
        }
        return [
            'begin_date' => $timestamp ? strtotime($dates[0]) : $dates[0],
            'end_date' => $timestamp ? strtotime($dates[1]) : $dates[1]
        ];
    }

    /**
     * 获取用户购买商品清单
     * @return mixed
     * @throws ErrorException
     */
    public function getUserOrderCommodity()
    {
        $access_token = Yii::$app->request->get('access-token');
        $user = UserCus::findIdentityByAccessToken($access_token);
        if ($user) {
            $data['item'] = $user->getOrderItems()
                ->select('commodity_id')
                ->where(['is_seckill' => 0])
                ->asArray()
                ->groupby('commodity_id')
                ->orderBy('rand()')
                ->limit(20)
                ->all();
            $ids = array_column($data['item'], 'commodity_id');
            $detail = Commodity::find()
                ->select('id as commodity_id,name as commodity_name,price,unit,is_seckill,pic')
                ->where(['in', 'id', $ids])
                ->andWhere(['is_online' => 1])
                ->asArray()
                ->limit(8)
                ->all();
            foreach ($detail as &$vs) {
                $vs['pic'] = explode(':;', $vs['pic'])[0];
            }
            $count = count($detail);
            $limit = 16 - $count;
            $comment = Commodity::find()
                ->select('id as commodity_id,name as commodity_name,price,unit,is_seckill,pic')
                ->where(['is_online' => 1])
                ->limit($limit)
                ->orderBy('rand()')
                ->asArray()
                ->all();
            foreach ($comment as &$v) {
                $v['pic'] = explode(':;', $v['pic'])[0];
            }
            $data['item'] = array_merge($detail, $comment);
            $data['recent_order'] = Order::find()
                ->with('details')
                ->where(['user_id' => $user->id])
                ->orderBy('id DESC')
                ->asArray()
                ->one();
            if (is_array($data['recent_order']))
                $data['recent_order']['create_time'] = date('Y-m-d H:i:s', $data['recent_order']['create_time']);
            return $data;
        }
        throw new ErrorException('用户不存在', 400);
    }


    /**
     * 保存订单
     * @return mixed
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $order = new CusOrder();
        $order->attributes = Yii::$app->request->post();//属性赋值
        $order = $this->initOrderData($order);//初始化订单数据
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $order->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$order->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $order->getErrors()];
        }
        //分配配送员
        $deliveryman = $this->distributeDeliveryman($post['store_id']);
        if ($deliveryman) {
            $order->driver_id = $deliveryman['id'];
            $order->driver_name = $deliveryman['name'];
        }
        if ($res = $order->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($order->commodity_list, $order);
            if ($res_detail['code'] == 400) {
                $t->rollBack();
                return ['code' => 400, 'msg' => '商品数据保存失败', 'data' => $res_detail['data']];
            }
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $this->setCouponStatus($order->coupon_id);//更改优惠券状态
        $this->clearShopCart($order->id);
        $t->commit();//提交事务
        return [
            'code' => '200',
            'msg' => '下单成功',
            'data' => [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'price' => $order->pay_price,
                'balance' => $this->user['balance']
            ]
        ];
    }

    /**
     * 初始化订单数据
     * @param $model
     * @return mixed
     */
    private function initOrderData($model)
    {
        $status = 1;
        $model->order_no = $this->getOrderNo();
        $model->user_id = $this->user['id'];
        $model->user_name = $this->user['username'];
        $model->nick_name = $this->user['nickname'];
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = isset($this->pay_way_text[$this->pay_way]) ? $this->pay_way_text[$this->pay_way] : '微信付款';
        $model->source_txt = isset($this->source_text[$this->source]) ? $this->source_text[$this->source] : '小程序';
        $model->create_time = time();
        return $model;
    }


    /**
     * 插入订单相关数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $order)
    {

        $orderDetail = new CusOrderDetail();
        $b = \Yii::$app->db->beginTransaction();
        $commodity_list = json_decode($commodity_list, true);
        foreach ($commodity_list as $attribute) {
            $_model = clone $orderDetail; //克隆对象,防止只插入一条数据
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->order_id = $order->id;
            $_model->total_price = $_model->price * $_model->num;
            if (!$_model->save()) {
                var_dump($_model->getErrors());
                $b->rollBack();
                return [
                    'code' => 400,
                    'msg' => '订单提交失败，请稍后再重试',
                    'data' => $orderDetail->getErrors()
                ];
            }
        }
        $b->commit();
        return [
            'code' => 200,
            'msg' => 'ok'
        ];
    }

    /**
     * 分配配送员
     * @param $store_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function distributeDeliveryman($store_id)
    {
        $model = UserDelivery::find();
        $query = $model
            ->select('id,nickname as name,mobile,head_pic')
            ->where(['store_id' => $store_id,'type'=>1])
            ->asArray()
            ->orderBy(['rand()' => SORT_DESC])
            ->one();
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
            $order_no = 'DD' . date('YmdHis') . mt_rand(100000, 999999);
            $exist_order_no = Order::find()->where(['order_no' => $order_no])->exists();
            if (!$exist_order_no) {
                break;
            }
        }
        return $order_no;
    }


    /**
     * 获取订单商品统计
     * @return mixed
     */
    public function getOrderStatistics()
    {
        $user = $this->user;
        $post = Yii::$app->request->post();
        if ($user) {
            if (isset($post['startDate'])) {
                $startDate = strtotime($post['startDate']);
            } else {
                $startDate = null;
            }
            if (isset($post['endDate'])) {
                $endDate = strtotime($post['endDate']) + 86400 - 1;
            } else {
                $endDate = null;
            }
            $model = $user->getOrderItems();
            $queryProduct = $model->select('commodity_id as id,commodity_name as name,sum(total_price) as total_price,unit,sum(num) AS num')
                ->where(['is_seckill' => 0])
                ->andFilterWhere(['between', 'create_time', $startDate, $endDate])
                ->asArray()
                ->groupby('commodity_id')
                ->all();
            $queryType = $model->select('parent_type_name as name,sum(price) as value')
                ->where(['is_seckill' => 0])
                ->andwhere(['<>', 'type_name', ''])
                ->andFilterWhere(['between', 'create_time', $startDate, $endDate])
                ->groupby('type_first_tier_id')
                ->asArray()
                ->all();
            $total = array_sum(array_column($queryType, 'value'));
            foreach ($queryType as &$v) {
                $v['ratio'] = round(($v['value'] / $total) * 100, 2);
            }
            $data['productData'] = $queryProduct;
            $data['typeList'] = $queryType;
            return $data;
        }
        throw new ErrorException('用户不存在', 400);
    }

    /**
     * 设置优惠券使用状态
     * @param $id
     */
    public function setCouponStatus($id)
    {
        if ($id) {
            $model = CusDiscountCouponGetRecord::findOne($id);
            $model->is_use = 1;
            $model->save();
        }
    }


    /**
     * 清除购物车数据
     * @param $orderId
     */
    public function clearShopCart($orderId)
    {
        $form = new CusShopcartForm();
        $form->clearShopCart($orderId);
    }


    /**
     * 获取用户订单状态统计
     * @return mixed
     */
    public function getOrderStatus()
    {
        $query = $this->user
            ->getOrders()
            ->select([
                'SUM( IF(status = "1", 1, 0) ) AS wait_pay',
                'SUM( IF(status = "2", 1, 0) ) AS wait_send',
                'SUM( IF(status = "3", 1, 0) ) AS wait_get',
                'SUM( IF(status = "4", 1, 0) ) AS wait_evaluate',
            ])
            ->asArray()
            ->all();
        return $query;
    }

}
