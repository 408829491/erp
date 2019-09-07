<?php

namespace app\models\form;

use app\models\FinanceAccountSettle;
use app\models\FinanceAccountSettleDetail;
use app\models\FinanceAccountSettlePurchase;
use app\models\FinanceAccountSettlePurchaseDetail;
use app\models\FinanceBalance;
use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Purchase;
use app\models\PurchaseAudit;
use common\models\User;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;

class FinanceForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_pay;
    public $pay_way;
    public $is_audit;
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
        '3' => '采购收货',
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
    public $purchase_pay_type = [
        '0' => '微信',
        '1' => '支付宝',
        '2' => '转账',
        '3' => '现金',
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 1,],
            [['source',], 'default','value'=>''],
            [['is_pay',], 'default','value'=>''],
            [['is_audit',], 'default','value'=>''],
            [['pay_way',], 'default','value'=>''],
            [['create_time',], 'default','value'=>''],
            [['delivery_date',], 'default','value'=>''],
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
        if($this->status){
            $query->andWhere([
                'status'=>$this->status
            ]);
        }
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','nick_name',$this->keyword],
                ['like','receive_tel',$this->keyword],
                ['like','driver_name',$this->keyword],
            ]);
        }
        if($this->delivery_date) {
            $query->andwhere([
                'and',
                [
                    '>=','delivery_date',
                    $this->dateFormat($this->delivery_date)['begin_date']
                ],
                [
                    '<=','delivery_date',
                    $this->dateFormat($this->delivery_date)['end_date']
                ],
            ]);
        }
        if($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=','create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=','create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }
        if(!empty($this->is_audit)) {
            $query->andWhere([
                'is_audit'=>$this->is_audit
            ]);
        }
        if(!empty($this->pay_way)) {
            $query->andWhere([
                'pay_way'=>$this->pay_way
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as $k=>&$v){
            $v['need_pay']= $v['price'] - $v['pay_price'];
            $v['settlement_type']= $this->settlement_type[1];
            $v['settlement_text']= $this->settlement_text[$v['is_pay']];
            $v['audit_text']= $this->audit_text[$v['is_audit']];
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 结算单列表
     * @return array
     */
    public function settleList()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = FinanceAccountSettle::find();
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','user_name',$this->keyword],
                ['like','receive_tel',$this->keyword],
                ['like','driver_name',$this->keyword],
            ]);
        }
        if($this->delivery_date) {
            $query->andwhere([
                'and',
                [
                    '>=','delivery_date',
                    $this->dateFormat($this->delivery_date)['begin_date']
                ],
                [
                    '<=','delivery_date',
                    $this->dateFormat($this->delivery_date)['end_date']
                ],
            ]);
        }
        if($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=','create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=','create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as $k=>&$v){
            $v['create_time']= date('Y-m-d H:i:s',$v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }



    /**
     * 采购结算单列表
     * @return array
     */
    public function settlePurchaseList()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = FinanceAccountSettlePurchase::find();
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','agent_name',$this->keyword],
            ]);
        }
        if($this->pay_way!='') {
            $query->andWhere([
                'pay_way'=>$this->pay_way
            ]);
        }

        if($this->create_time) {
            $query->andwhere([
                'and',
                [
                    '>=','create_time',
                    $this->dateFormat($this->create_time)['begin_date']
                ],
                [
                    '<=','create_time',
                    $this->dateFormat($this->create_time)['end_date']
                ],
            ]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as $k=>&$v){
            $v['create_time']= date('Y-m-d H:i:s',$v['create_time']);
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
    public function updateOrderStatus($order_id,$pay_price){
            $model = Order::findOne((int)$order_id);
            $model->is_pay = 'Y';
            $model->pay_price = $pay_price;
            $model->status = 1;
            $model->is_pay_text = $this->pay_text['Y'];
            if($model->save()){
                return true;
            }
        return $model->getErrors();
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval,$timestamp = 1){
        $date_interval = explode(' - ',$date_interval);
        return [
            'begin_date'=>$timestamp?strtotime($date_interval[0]):$date_interval[0],
            'end_date'=>$timestamp?strtotime($date_interval[1]):$date_interval[1]
        ];
    }


    /**
     * 生成结算单
     * @return array
     * @throws \yii\db\Exception
     */
    public function save(){
        $post = Yii::$app->request->post();
        $settle = new FinanceAccountSettle();
        $settle->attributes = $post;//属性赋值
        $settle = $this->initSettleData($settle);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $settle->list = isset($post['list'])?$post['list']:[];
        if (!$settle->validate() || !$settle->list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $settle->getErrors()];
        }
        $total = $this->getSummary($settle->list);
        $settle->price = $total['needSum'];
        $settle->actual_price = $total['receiptsSum'];
        if($res = $settle->save()){
            //保存业务单数据
            $res_detail = $this->insertData($settle->list,$settle->id);
            if($res_detail['code'] == 400){
                $t->rollBack();
                return ['code'=>400,'msg'=>'数据保存失败','data'=>$res_detail['data']];
            }
        }else{
            $t->rollBack();
            return ['code'=>400,'msg'=>'数据保存失败','data'=>[]];
        }
        $t->commit();//提交事务
        $this->updateOrderStatus($settle->list[0]['id'],$settle->actual_price);//更新订单状态
        return $res;
    }



    /**
     * 生成采购结算单
     * @return array
     * @throws \Yii\db\Exception
     */
    public function savePurchase(){
        $post = Yii::$app->request->post();
        $settle = new FinanceAccountSettlePurchase();
        $settle->attributes = $post;//属性赋值
        $settle = $this->initSettlePurchaseData($settle);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $settle->list = isset($post['list'])?$post['list']:[];
        if (!$settle->validate() || !$settle->list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $settle->getErrors()];
        }
        $total = $this->getSummary($settle->list);
        $settle->price = $total['needSum'];
        $settle->refer_no = $post['purchase_no'];
        $settle->actual_price = $total['receiptsSum'];
        if($res = $settle->save()){
            //保存业务单数据
            $res_detail = $this->insertData($settle->list,$settle->id,3);
            if($res_detail['code'] == 400){
                $t->rollBack();
                return ['code'=>400,'msg'=>'数据保存失败','data'=>$res_detail['data']];
            }
        }else{
            $t->rollBack();
            return ['code'=>400,'msg'=>'数据保存失败','data'=>[]];
        }
        $t->commit();//提交事务
        $this->updateAuditStatus($post['id'],$post['actual_price']);
        return $res;
    }


    /**
     * 更新对账单结算状态
     * @param $order_id
     * @param $status
     * @return mixed
     */
    public function updateAuditStatus($audit_id,$price){
        $model = PurchaseAudit::findOne((int)$audit_id);
        $model->settle_price += $price;
        $model->settlement_time = time();
        if($model->save()){
            if($model->settle_price == $model->audit_price){
                $model->is_settlement = 1;
            }else{
                $model->is_settlement = 2;
            }
            $model->save();
            return true;
        }
        return $model->getErrors();
    }


    /**
     * 初始化结算单数据
     * @param $model
     * @return mixed
     */
    private function initSettleData($model){
        $status = 1;
        $model->settle_no = $this->getSettleNo('SE');
        $model->status = $status;
        $model->status_text = $this->status_text[$status];
        $model->pay_way_text = $this->pay_way_text[$model->pay_way];
        $model->create_time = time();
        return $model;
    }

    /**
     * 初始化结算单数据
     * @param $model
     * @return mixed
     */
    private function initSettlePurchaseData($model){
        $model->settle_no = $this->getSettleNo('SE');
        $model->pay_way_text = $this->purchase_pay_type[$model->pay_way];
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算订单总额
     * @param array $list
     * @return mixed
     */
    private function getSummary($list = []){
        $needSum = $receiptsSum = 0;
        foreach($list as $v){
            $needSum += $v['need_pay'];
            $receiptsSum += $v['actual_price'];
        }
        $total['needSum']=$needSum;
        $total['receiptsSum']=$receiptsSum;
        return $total;
    }

    /**
     * 插入结算单详细数据
     * @param $list
     * @param $id
     * @return array
     * @throws \yii\db\Exception
     */
    private function insertData($list,$id,$type = 1){
        if($type == 3){
            $detail = new FinanceAccountSettlePurchaseDetail();
        }else{
            $detail = new FinanceAccountSettleDetail();
        }

        $b = \Yii::$app->db->beginTransaction();
        foreach($list as $v){
            $_model = clone $detail; //克隆对象,防止只插入一条数据
            $settle_type=$type;
            $data = [
                'refer_no'=>($type==3)?$v['purchase_no']:$v['order_no'],
                'settle_id'=>$id,
                'bill_type'=>$settle_type,
                'bill_type_text'=>$this->settlement_type[$settle_type],
                'should_price'=>$v['need_pay'],
                'pay_price'=>$v['actual_price'],
                'actual_price'=>$v['actual_price'],
                'remark'=>$v['remark'],
                'create_time'=>time(),
            ];
            $_model->setAttributes($data);
            if (!$_model->save()) {//创建新记录
                $b->rollBack();
                return [
                    'code' => 400,
                    'msg' => '提交失败，请稍后再重试',
                    'data' =>$_model->getErrors()
                ];
            }
        }
        $b->commit();

        return [
            'code' => 200,
            'msg' => 'ok',
            'data' =>[]
        ];
    }

    /**
     * 生成单号
     * @param $prefix
     * @return null|string
     */
    public function getSettleNo($prefix)
    {
        return $prefix.date('YmdHis') . mt_rand(10000, 99999);
    }

    /**
     * 结算单详情
     * @param $id
     * @return mixed
     *
     */
    public function getSettleDetail($id){
        $query = FinanceAccountSettle::find()
            ->with('detail')
            ->where(['id'=>$id])
            ->asArray()->one();
        return $query;
    }

    /**
     * 采购结算单详情
     * @param $id
     * @return mixed
     *
     */
    public function getSettlePurchaseDetail($id){
        $query = FinanceAccountSettlePurchase::find()
            ->with('detail')
            ->where(['id'=>$id])
            ->asArray()->one();
        return $query;
    }

    /**
     * 获取客户列表
     * @return mixed
     */
    public function getUserBalance(){
        $this->attributes = Yii::$app->request->get();
        $query = User::find();
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','mobile',$this->keyword],
                ['like','nickname',$this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 获取客户应收应付
     * @return array
     */
    public function GetReceiptSum(){
        $this->attributes = Yii::$app->request->get();
        $query = Order::find();
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','receive_tel',$this->keyword],
                ['like','nick_name',$this->keyword],
            ]);
        }
        if($this->create_time) {
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

        $query->groupBy('user_id')
            ->orderBy('id DESC');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $query->asArray()
            ->select("user_id,nick_name,sum(price) as amount,sum(pay_price) as pay_amount,receive_name,(sum(price) - sum(pay_price)) as will_pay_amount,receive_tel")
            ->offset($pagination->offset);
        $count = $query->count();
        $data = $query->limit($pagination->pageSize)
            ->all();
        return [
            'total' => $count,
            'list' => $data,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 获取充值记录
     * @return array
     */
    public function getRechargeData($id){
        $this->attributes = Yii::$app->request->get();
        $query = FinanceBalance::find();
        $query->where(['type'=>0,'user_id'=>$id,'status'=>1]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list,function(&$data){
            $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 获取交易记录
     * @return array
     */
    public function getBalanceData($id = 0){
        $this->attributes = Yii::$app->request->get();
        $query = FinanceBalance::find();
        $query->where(['status'=>1]);
        $query->andFilterWhere(['user_id'=>$id]);
        if($this->keyword) {
            $query->andwhere([
                'or',
                ['like','pay_user',$this->keyword],
                ['like','tel',$this->keyword],
            ]);
        }
        if($this->create_time) {
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
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list,function(&$data){
            $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 扣款
     * @return mixed
     */
    public function saveBalance(){
        $post = Yii::$app->request->post();
        $balance = new FinanceBalance();
        $balance->recharge_no = $this->getSettleNo('RE');
        $balance->attributes = $post;
        $balance->amount = ($balance->type==0)?$balance->amount:($balance->amount*-1);
        $balance->current_balance += (float)$balance->amount;
        $balance->create_time = time();
        $balance->status = 1;
        if($res = $balance->save()){
            //更新用户账户余额
            $this->updateUserAccount($balance->user_id,$balance->amount);
            return $res;
        }
        return [
            'code' => 400,
            'msg' => array_values($balance->getErrors()),
            'data' =>[]
        ];
    }

    /**
     * 更新用户账户金额
     * @param $userId
     * @param $amount
     * @return bool
     */
    private function updateUserAccount($userId,$amount){
        if(User::updateAllCounters(['balance' => $amount],['id' => $userId])){
            return true;
        }
        return false;
    }

    /**
     * 采购结算记录
     * @param $referNo
     * @return array|\yii\db\ActiveRecord[]
     */
    public function purchaseSettlementRecord($referNo){
        $query = FinanceAccountSettlePurchase::find()->where(['refer_no'=>$referNo]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list,function(&$data){
            $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 收货多退少补的结算记录
     */
    public function createByOrder($order, $orderDetail) {
        // 添加多退少补结算单
        $diffValue = $this->calOrderDiffValue($orderDetail);
        // 如果差值为0 不做多退少补
        if ($diffValue == 0) {
            return ;
        }

        // 扣除余额
        $user = User::findOne($order['user_id']);
        $user->balance = $user->balance + $diffValue;
        if (!$user->save()) {
            throw new Exception($user->errors);
        }
        // 添加结算单主表
        $model = new FinanceAccountSettle();
        $model->settle_no = 'SE'.date('YmdHis').mt_rand(10000, 99999);
        $model->user_id = $order['user_id'];
        $model->user_name = $order['user_name'];
        $model->create_user_id = Yii::$app->user->identity['id'];
        $model->create_user = Yii::$app->user->identity['nickname'];
        $model->price = $diffValue;
        $model->actual_price = $diffValue;
        $model->remark = "多退少补";
        $model->create_time = time();
        if (!$model->save()) {
            throw new Exception($model->errors);
        }

        // 添加结算单子表
        $model2 = new FinanceAccountSettleDetail();
        $model2->settle_id = $model->id;
        $model2->refer_no = $order['order_no'];
        $model2->bill_type = 2;
        $model2->bill_type_text = UserAuditForm::addTypeText(2);
        $model2->should_price = $diffValue;
        $model2->pay_price = $diffValue;
        $model2->actual_price = $diffValue;
        $model2->reduction_price = 0;
        $model2->remark = "多退少补";
        $model2->create_time = time();
        if (!$model2->save()) {
            throw new \yii\db\Exception($model2->errors);
        }
    }

    // 根据订单子表计算订单多退少补的差值
    public function calOrderDiffValue($orderDetail) {
        $result = 0;
        foreach ($orderDetail as $item) {
            $result += ($item['num'] - $item['actual_num']) * $item['price'];
        }

        return $result;
    }
}
