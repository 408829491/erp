<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityProfile;
use app\models\Model;
use app\models\Purchase;
use app\models\PurchaseAudit;
use app\models\PurchaseBuyer;
use app\models\PurchaseDetail;
use app\models\PurchaseProvider;
use app\models\PurchaseRefund;
use app\models\PurchaseRefundDetail;
use app\models\StockIn;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;

class PurchaseRefundForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $delivery_date;
    public $plan_date;
    public $user;
    public $source;
    public $create_time;
    public $purchase_type;
    public $status_text = [
        1 => '待审核',
        3 => '已完成',
        4 => '已关闭',
    ];
    public $purchase_text = [
        0 => '市场自采',
        1 => '供应商供货'
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['source',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['plan_date',], 'default', 'value' => ''],
            [['purchase_type',], 'default', 'value' => ''],
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
        $query = PurchaseRefund::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'refund_no', $this->keyword],
                ['like', 'agent_name', $this->keyword],
            ]);
        }
        if ($this->plan_date) {
            $query->andwhere([
                'and',
                [
                    '>=', 'plan_date',
                    $this->dateFormat($this->plan_date, 0)['begin_date']
                ],
                [
                    '<=', 'plan_date',
                    $this->dateFormat($this->plan_date, 0)['end_date']
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

        if (!empty($this->source)) {
            $query->andWhere([
                'source' => $this->source
            ]);
        }

        if ($this->purchase_type === '1') {
            $query->andWhere([
                'purchase_type' => $this->purchase_type
            ]);
        } else if ($this->purchase_type === '0') {
            $query->andWhere([
                'purchase_type' => 0
            ]);
        };
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as $k => &$v) {
            $v['status_text'] = $this->status_text[$v['status']];
            $v['purchase_type_text'] = isset($this->purchase_text[$v['purchase_type']]) ? $this->purchase_text[$v['purchase_type']] : '';
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 更新订单状态
     * @param $purchase_id
     * @param $status
     * @return mixed
     */
    public function updatePurchaseStatus($refund_id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = PurchaseRefund::findOne($refund_id);
            $model->status = $status;
            if ($model->save()) {
                //生成出库单
                $this->confirmReceive($refund_id);
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return $model->getErrors();
    }


    /**
     * 确认收货生成出库单
     * @param $id ：采购单号
     */
    public function confirmReceive($id)
    {
        $model = new StockOutForm();
        $purchase = PurchaseRefund::find()->with('details')->asArray()->where(['id' => $id])->one();
        $details = $purchase['details'];
        foreach($details as &$v){
            $v['price'] = $v['refund_price'];
            $v['num'] = $v['refund_num'];
        }
        $post['about_no'] = $purchase['refund_no'];
        $post['about_id'] = $purchase['id'];
        $post['out_time'] = date('Y-m-d H:i:s',time());
        $post['type'] = 4;//出库类型：退货出库
        $post['status'] = 1; //入库状态：已出库
        $post['commodity_list'] = $details;
        $trans = Yii::$app->db->beginTransaction();
        try{
            $model->createStock($post);//创建出库单
            $trans->commit();
        }catch (Exception $exception){
            $trans->rollBack();
            throw new Exception($exception->getMessage());
        }

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
     * @return mixed
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $purchase = ($id) ? PurchaseRefund::findOne($post['id']) : new PurchaseRefund();
        $purchase->attributes = $post;//属性赋值
        if(!$id){
            $purchase = $this->initPurchaseData($purchase);
        }
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $purchase->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$purchase->validate() || !$purchase->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $purchase->getErrors()];
        }
        $purchase->refund_num = count($purchase->commodity_list);
        $purchase->price = $this->getSummary($purchase->commodity_list);
        if ($res = $purchase->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($purchase->commodity_list, $purchase, $id);
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
    private function initPurchaseData($model)
    {
        $status = 1;
        $model->refund_no = $this->getRefundNo();
        $model->status = $status;
        $model->author = Yii::$app->user->identity['username'];
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
            $total += $v['refund_price'] * (int)$v['refund_num'];
        }
        return $total;
    }

    /**
     * 插入订单商品数据
     * @param $commodity_list商品数据
     */
    private function insertData($commodity_list, $purchase, $id = 0)
    {
        $purchaseDetail = new PurchaseRefundDetail();
        $purchaseDetail->updateAll(['is_delete' => 1], ['refund_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $purchaseDetail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'refund_num' => $attribute['refund_num'],
                    'refund_price' => $attribute['refund_price'],
                    'total_refund_price' => $attribute['refund_num'] * $attribute['refund_price'],
                    'remark' => $attribute['remark'],
                    'is_delete' => 0,
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->refund_id = $purchase->id;
            $_model->total_refund_price = $_model->refund_price * $_model->refund_num;
            if (!$_model->save()) {
                throw new Exception($_model->getErrors());
            }
        }
    }

    /**
     * 生成订单号
     * @return null|string
     */
    public function getRefundNo()
    {
        $refund_no= null;
        while (true) {
            $refund_no = 'RF' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_refund_no = PurchaseRefund::find()->where(['refund_no' => $refund_no])->exists();
            if (!$exist_refund_no) {
                break;
            }
        }
        return $refund_no;
    }

}
