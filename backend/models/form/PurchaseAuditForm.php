<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\Model;
use app\models\Purchase;
use app\models\PurchaseAudit;
use app\models\PurchaseAuditDetail;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;

class PurchaseAuditForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $is_audit;
    public $delivery_date;
    public $user;
    public $source;
    public $create_time;
    public $is_settlement;
    public $purchase_type;
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
            [['is_audit',], 'default', 'value' => 0,],
            [['create_time',], 'default', 'value' => ''],
            [['is_settlement',], 'default', 'value' => ''],
            [['purchase_type',], 'default', 'value' => ''],
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
        $query = PurchaseAudit::find();
        if ($this->is_settlement) {
            $query->andWhere([
                'is_settlement' => $this->is_settlement
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'purchase_no', $this->keyword],
                ['like', 'agent_name', $this->keyword],
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

        if ($this->purchase_type!='') {
            $query->andWhere([
                'purchase_type' => $this->purchase_type
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
            $v['purchase_type_text'] = isset($this->purchase_text[$v['purchase_type']]) ? $this->purchase_text[$v['purchase_type']] : '';
            $v['no_pay_price'] = round($v['audit_price'] - $v['settle_price'], 2);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 获取对账单
     * @param $id
     * @return array
     */
    public function getData($id)
    {
        $query = PurchaseAudit::find()
            ->with([
                'details' => function ($query) {
                    $query->select('*,commodity_name as name')
                        ->andWhere('is_delete=0');
                },
            ])
            ->where(['id' => $id])
            ->asArray()
            ->one();
        $query['reduction_price'] = 0;
        $query['actual_price'] = $query['need_pay'] = round($query['audit_price'] - $query['settle_price'], 2);
        $query['create_time'] = date('Y-m-d H:i:s', $query['create_time']);
        return $query;
    }

    /**
     * 更新订单状态
     * @param $purchase_id
     * @param $status
     * @return mixed
     */
    public function updatePurchaseStatus($audit_id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = PurchaseAudit::findOne($audit_id);
            $model->status = $status;
            if ($model->save()) {
                $this->confirmReceive($audit_id);//生成入库单
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
    public function save($post = [])
    {
        if (!$post) {
            $post = Yii::$app->request->post();
        }
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        if ($id) {
            $purchase = PurchaseAudit::findOne($post['id']);
            $purchase->is_audit = 1;
            $purchase->audit_time = time();
            $purchase->author = Yii::$app->user->identity['username'];
        } else {
            $purchase = new PurchaseAudit();
            $purchase->audit_no = $this->getAuditNo();
        }
        $purchase->attributes = $post;//属性赋值
        $purchase = $this->initPurchaseData($purchase);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $purchase->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$purchase->validate() || !$purchase->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $purchase->getErrors()];
        }
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase->audit_price = $purchase->price = $this->getSummary($purchase->commodity_list);
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
            $total += $v['diff_price'] * (int)$v['diff_num'];
        }
        return $total;
    }

    /**
     * 插入商品数据
     * @param $commodity_list商品数据
     */
    private function insertData($commodity_list, $audit, $id = 0)
    {
        $purchaseDetail = new PurchaseAuditDetail();
        $purchaseDetail->updateAll(['is_delete' => 1], ['audit_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $purchaseDetail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'diff_price' => $attribute['diff_price'],
                    'diff_num' => $attribute['diff_num'],
                    'diff_total_price' => $attribute['diff_total_price'],
                    'is_delete' => 0,
                    'update_at' => time(),
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->audit_id = $audit->id;
            $_model->purchase_total_price = $_model->purchase_price * $_model->purchase_num;
            if (!$_model->save()) {
                throw new Exception($_model->getErrors());
            }
        }
    }

    /**
     * 生成单号
     * @return null|string
     */
    public function getAuditNo()
    {
        $purchase_no = null;
        while (true) {
            $purchase_no = 'CD' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_purchase_no = Purchase::find()->where(['purchase_no' => $purchase_no])->exists();
            if (!$exist_purchase_no) {
                break;
            }
        }
        return $purchase_no;
    }


    /**
     * 初始化商品数据
     * @param $model
     * @return mixed
     */
    private function initCommodityData($model)
    {
        $commodity_list = [];
        $info = array_column($model->commodity_list, 'total_num', 'commodity_id');
        $ids = array_keys($info);
        $query = Commodity::find()->select('bn_commodity.id,bn_commodity.name,bn_commodity.type_id,bn_commodity.type_first_tier_id,bn_commodity.name,bn_commodity.unit,bn_commodity.pic,price,bn_commodity_category.name as type_name')->leftJoin('bn_commodity_category', 'bn_commodity_category.id=bn_commodity.type_id')->where(['in', 'bn_commodity.id', $ids])->asArray()->all();
        foreach ($query as $k => $v) {
            $commodity_list[$k]['commodity_id'] = $v['id'];
            $commodity_list[$k]['name'] = $v['name'];
            $commodity_list[$k]['commodity_name'] = $v['name'];
            $commodity_list[$k]['price'] = $v['price'];
            $commodity_list[$k]['unit'] = $v['unit'];
            $commodity_list[$k]['purchase_num'] = $info[$v['id']];
            $commodity_list[$k]['type_name'] = $v['type_name'];
            $commodity_list[$k]['type_id'] = $v['type_id'];
            $commodity_list[$k]['type_first_tier_id'] = $v['type_first_tier_id'];
            $commodity_list[$k]['num'] = 0;
            $commodity_list[$k]['purchase_price'] = $v['price'];
            $commodity_list[$k]['pic'] = $v['pic'];
            $commodity_list[$k]['remark'] = '订单汇总';
        }
        $model->commodity_list = $commodity_list;
        $model['agent_id'] = 1;
        $model['agent_name'] = '陶杰';
        return $model;
    }


    /**
     * 订单汇总生成采购单
     * @return mixed
     */
    public function OrderToPurchaseSave()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $purchase = new Purchase();
        $purchase->attributes = $post;
        $purchase->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        $purchase->purchase_no = $this->getAuditNo();
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase = $this->initCommodityData($purchase);
        $purchase = $this->initPurchaseData($purchase);
        $purchase->purchase_num = count($purchase->commodity_list);
        $purchase->price = $this->getSummary($purchase->commodity_list);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $purchase->save();
            $this->insertData($purchase->commodity_list, $purchase);
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * 结算生成结算单
     * @param $id ：对账单号
     */
    public function confirmReceive($id)
    {
        $model = new StockInForm();
        $purchase = Purchase::find()->with('details')->asArray()->where(['id' => $id])->one();
        $details = $purchase['details'];
        foreach ($details as &$v) {
            $v['num'] = $v['purchase_num'];
        }
        $post['purchase_no'] = $purchase['purchase_no'];
        $post['in_time'] = time();
        $post['store_id_name'] = '默认仓库';
        $post['type'] = 1;//入库类型：采购入库
        $post['status'] = 1; //入库状态：已入库
        $post['commodity_list'] = $details;
        $model->createStock($post);
    }
}
