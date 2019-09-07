<?php

namespace app\models\form;

use app\models\CusGroupOrder;
use app\models\CusOrder;
use app\models\FinanceAccountSettle;
use app\models\FinanceAccountSettleDetail;
use app\models\FinanceBalance;
use app\models\Order;
use common\models\User;
use common\models\UserCus;
use Yii;


class CusFinanceForm extends \yii\base\Model
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
    public $settlement_text = [
        'N' => '未结算',
        'Y' => '已结算',
    ];
    public $pay_text = [
        'N' => '未付款',
        'Y' => '已付款',
    ];
    public $settlement_type = [
        '1' => '销售订单',
        '2' => '运费',
    ];

    public $audit_text = [
        '0' => '未对账',
        '1' => '已对账',
    ];
    public $status_text = [
        '0' => '已审核',
        '1' => '未审核',
    ];
    public $pay_way_text = [
        '0' => '货到付款',
        '1' => '在线支付',
        '2' => '余额支付',
    ];
    public $balance_type = [
        '0' => '充值',
        '1' => '扣款',
        '2' => '订单支付',
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
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
        ];
    }


    /**
     * 更新订单状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updateOrderStatus($order_id, $pay_price)
    {
        $model = Order::findOne((int)$order_id);
        $model->is_pay = 'Y';
        $model->pay_price = $pay_price;
        $model->status = 2;
        $model->is_pay_text = $this->pay_text['Y'];
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }


    /**
     * 更新门店订单状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updateCusOrderStatus($order_id, $pay_price)
    {
        $model = CusOrder::findOne((int)$order_id);
        $model->is_pay = 'Y';
        $model->pay_price = $pay_price;
        $model->status = 2;
        $model->status_text = '待配送';
        $model->is_pay_text = $this->pay_text['Y'];
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }


    /**
     * 更新门店订单状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updateCusGroupOrderStatus($order_id, $pay_price)
    {
        $model = CusGroupOrder::findOne((int)$order_id);
        $model->is_pay = 'Y';
        $model->pay_price = $pay_price;
        $model->status = 2;
        $model->status_text = '待配送';
        $model->is_pay_text = $this->pay_text['Y'];
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }



    /**
     * 生成结算单
     * @return array
     */
    public function save($model,$type=0)
    {
        $settle = new FinanceAccountSettle();
        //组织结算单数据
        $data = [
            'pay_way' => '2',
            'pay_user' => $model['user_name'],
            'create_user' => $model['user_name'],
            'user_name' => $model['nick_name'],
            'price' => $model['price'],
            'actual_price' => $model['price'],
            'reduction_price' => 0,
            'remark' => '订单支付',
            'list' => [[
                'order_no' => $model['order_no'],
                'need_pay' => $model['price'],
                'pay_price' => $model['price'],
                'actual_price' => $model['price'],
                'remark' => '',
            ]]
        ];
        $settle->attributes = $data;//属性赋值
        $settle = $this->initSettleData($settle);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $settle->list = $data['list'];
        if (!$settle->validate() || !$settle->list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $settle->getErrors()];
        }
        $total = $this->getSummary($settle->list);
        $settle->price = $total['needSum'];
        $settle->actual_price = $total['receiptsSum'];
        if ($res = $settle->save()) {
            //保存业务单数据
            $res_detail = $this->insertData($settle->list, $settle->id);
            if ($res_detail['code'] == 400) {
                $t->rollBack();
                return ['code' => 400, 'msg' => '数据保存失败', 'data' => $res_detail['data']];
            }
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $t->commit();//提交事务
        if($type==1){
            $this->updateCusOrderStatus($model['id'], $settle->actual_price);//更新订单状态
        }else if($type==2){
            $this->updateCusGroupOrderStatus($model['id'], $settle->actual_price);//更新订单状态
        }
        else{
            $this->updateOrderStatus($model['id'], $settle->actual_price);//更新订单状态
        }

        return $res;
    }


    /**
     * 初始化结算单数据
     * @param $model
     * @return mixed
     */
    private function initSettleData($model)
    {
        $status = 1;
        $model->settle_no = $this->getSettleNo('SE');
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = $this->pay_way_text[$model->pay_way];
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算订单总额
     * @param array $commodity_list
     * @return mixed
     */
    private function getSummary($list = [])
    {
        $needSum = $receiptsSum = 0;
        foreach ($list as $v) {
            $needSum += $v['need_pay'];
            $receiptsSum += $v['actual_price'];
        }
        $total['needSum'] = $needSum;
        $total['receiptsSum'] = $receiptsSum;
        return $total;
    }

    /**
     * 插入订单商品数据
     * @param $commodity_list商品数据
     * @return mixed
     */
    private function insertData($list, $id)
    {
        $detail = new FinanceAccountSettleDetail();
        $b = \Yii::$app->db->beginTransaction();
        foreach ($list as $v) {
            $_model = clone $detail; //克隆对象,防止只插入一条数据
            $settle_type = 1;
            $data = [
                'refer_no' => $v['order_no'],
                'settle_id' => $id,
                'bill_type' => $settle_type,
                'bill_type_text' => $this->settlement_type[$settle_type],
                'should_price' => $v['need_pay'],
                'pay_price' => $v['pay_price'],
                'actual_price' => $v['actual_price'],
                'remark' => $v['remark'],
                'create_time' => time(),
            ];
            $_model->setAttributes($data);
            if (!$_model->save()) {//创建新记录
                $b->rollBack();
                return [
                    'code' => 400,
                    'msg' => '提交失败，请稍后再重试',
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
     * 生成单号
     * @param $prefix
     * @return null|string
     */
    public function getSettleNo($prefix)
    {
        return $prefix . date('YmdHis') . mt_rand(10000, 99999);
    }


    /**
     * 生成交易流水
     * @param $data
     * @return array|bool
     */
    public function savesBalance($model, $type = 2)
    {
        //组织数据
        $data = [
            'amount' => $model['price'],
            'current_balance' => $model['balance'],
            'user_id' => $model['user_id'],
            'user_name' => $model['user_name'],
            'refer_no' => $model['order_no'],
            'op_user' => $model['user_name'],
            'pay_user' => $model['nick_name'],
            'type' => $type,//0充值，1扣款，2订单支付
            'remark' => ($type == 0) ? '充值' : '订单支付扣款',
        ];
        $balance = new FinanceBalance();
        $balance->recharge_no = $this->getSettleNo('RE');
        $balance->attributes = $data;
        $balance->amount = ($balance->type == 0) ? $balance->amount : ($balance->amount * -1);
        $balance->current_balance += (float)$balance->amount;
        $balance->create_time = time();
        if ($res = $balance->save()) {
            return $balance->recharge_no;
        }
        return false;
    }

    /**
     * 更新流水单状态
     */
    public function updateBalanceState($rechargeNo)
    {
        $model = FinanceBalance::findOne(['recharge_no' => $rechargeNo]);
        $model->status = 1;
        if ($model->save()) {
            return true;
        }
        return false;
    }


    /**
     * 更新用户账户金额
     * @param $userId
     * @param $amount
     * @return bool
     */
    public function updateUserAccount($userId, $amount)
    {
        if (UserCus::updateAllCounters(['balance' => $amount], ['id' => $userId])) {
            return true;
        }
        return false;
    }



    /**
     * 更新用户账户金额累计
     * @param $userId
     * @param $amount
     * @return bool
     */
    public function updateCusUserAccount($userId, $amount)
    {
        if (UserCus::updateAllCounters(['balance' => $amount], ['id' => $userId])) {
            return true;
        }
        return false;
    }


    public function updateCusUserAccountReplace($userId, $amount){
        $model = UserCus::findOne($userId);
        $model->balance = $amount;
        if($model->save()){
            return true;
        }
        return false;
    }

    /**
     * 更新用户积分
     * @return bool
     */
    public function updateCusUserIntegral($amount,$userId){
        if (UserCus::updateAllCounters(['integral' => $amount], ['id' => $userId])) {
            return true;
        }
        return false;
    }

}
