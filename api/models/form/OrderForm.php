<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\DeliveryLine;
use app\models\Order;
use app\models\OrderDetail;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;
use yii\db\Query;

class OrderForm extends \yii\base\Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $create_time;
    public $achieve_date;
    public $delivery_date;
    public $user;
    public $source;
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

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['is_pay',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
        ];
    }

    public static function tableName()
    {
        return '{{%order}}';
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
        $query = Order::find();
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
            $query->andWhere([
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
        $query = Order::find()->with('details')->where(['id' => $order_id])->asArray()->one();
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
            $model = Order::findOne($order_id);
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
        $model = Order::findOne($order_id);
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
            $model = Order::findOne($order_id);
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
        $user = User::findIdentityByAccessToken($access_token);
        $c_type_id = $user['c_type_id'];
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
                ->select('bn_commodity.id as commodity_id,bn_commodity.name as commodity_name,bn_commodity.unit as base_unit,bn_commodity_profile_detail.price,bn_commodity_profile.name as unit,is_seckill,pic,is_basics_unit,base_self_ratio')
                ->leftJoin('bn_commodity_profile','bn_commodity_profile.commodity_id = bn_commodity.id')
                ->leftJoin('bn_commodity_profile_detail','bn_commodity_profile.id=bn_commodity_profile_detail.commodity_profile_id and bn_commodity_profile_detail.type_id ='.$c_type_id)
                ->where(['in', 'bn_commodity.id', $ids,'is_sell'=>1])
                ->andWhere(['is_online' => 1])
                ->groupBy('bn_commodity_profile.commodity_id')
                ->asArray()
                ->limit(8)
                ->all();
            foreach ($detail as &$vs) {
                $vs['pic'] = explode(':;', $vs['pic'])[0];
            }
            $count = count($detail);
            $limit = 16 - $count;
            $comment = Commodity::find()
                ->select('bn_commodity.id as commodity_id,bn_commodity.name as commodity_name,bn_commodity.unit as base_unit,bn_commodity_profile_detail.price,bn_commodity_profile.name as unit,is_seckill,pic,is_basics_unit,base_self_ratio')
                ->leftJoin('bn_commodity_profile','bn_commodity_profile.commodity_id = bn_commodity.id')
                ->leftJoin('bn_commodity_profile_detail','bn_commodity_profile.id=bn_commodity_profile_detail.commodity_profile_id and bn_commodity_profile_detail.type_id ='.$c_type_id)
                ->where(['is_online' => 1,'is_sell'=>1])
                ->limit($limit)
                ->orderBy('rand()')
                ->groupBy('bn_commodity_profile.commodity_id')
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
        $order = new Order();
        $order->attributes = Yii::$app->request->post();//属性赋值
        $order = $this->initOrderData($order);//初始化订单数据
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $order->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$order->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $order->getErrors()];
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
        $t->commit();//提交事务
        return [
            'code' => '200',
            'msg' => '下单成功',
            'data' => [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'price' => $order->price,
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
        $model->receive_tel = $this->user['mobile'];
        $model->receive_name = $this->user['nickname'];
        $model->nick_name = $this->user['nickname'];
        $model->address_detail = $this->user['address'];
        $model->c_type = $this->user['c_type_id'];
        $model->pay_price = 0;
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = $this->pay_text['N'];
        $model->source_txt = isset($this->source_text[$this->source]) ? $this->source_text[$this->source] : '小程序';
        $lineInfo = $this->getLineInfo($model->user_id);
        if($lineInfo){
            $model->driver_id = $lineInfo['driver_id'];
            $model->driver_name = $lineInfo['driver_name'];
            $model->line_id = $lineInfo['id'];
            $model->line_name = $lineInfo['name'];
        }
        $model->create_time = time();
        return $model;
    }

    /**
     * 获取线路信息
     * @param $driverId
     * @return array
     */
    public function getLineInfo($user_id){
        $lineInfo = User::findOne($user_id);
        $query = DeliveryLine::findOne($lineInfo['line_id'])->toArray();
        return $query;
    }



    /**
     * 插入订单相关数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $order)
    {

        $orderDetail = new OrderDetail();
        $b = \Yii::$app->db->beginTransaction();
        $commodity_list = json_decode($commodity_list, true);
        $types = $this->getPurchaseType($commodity_list);
        foreach ($commodity_list as $attribute) {
            $_model = clone $orderDetail; //克隆对象,防止只插入一条数据
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->c_type = $this->user['c_type_id'];
            $_model->order_id = $order->id;
            $_model->channel_type = isset($types[$attribute['commodity_id']])?$types[$attribute['commodity_id']]:0;
            $_model->delivery_date = $order->delivery_date;
            $_model->total_price = $_model->price * $_model->num;
            if (!$_model->save()) {
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
     * 获取采购类型
     * @param array $commodity_list
     * @return array
     */
    public function getPurchaseType($commodity_list = []){
        $ids = array_column($commodity_list,'commodity_id');
        $query = Commodity::find()->select('id,channel_type')->where(['in','id',$ids])->asArray()->all();
        return array_column($query,'channel_type','id');
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
     * 获取用户订单状态统计
     * @return mixed
     */
    public function getOrderStatus()
    {
        $query = $this->user
            ->getOrders()
            ->select([
                'SUM( IF(status = "1", 1, 0) ) AS total_send',
                'SUM( IF(status = "2", 1, 0) ) AS total_receive',
                'SUM( IF(status = "3", 1, 0) ) AS total_finish',
                'SUM( IF(is_pay = "N", 1, 0) ) AS total_unpaid',
            ])
            ->asArray()
            ->all();
        return $query;
    }
}
