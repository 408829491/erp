<?php

namespace app\models\form;

use app\models\CommodityProfile;
use app\models\Model;
use app\models\OrderDetail;
use app\models\Purchase;
use app\models\PurchaseBuyer;
use app\models\PurchaseDetail;
use app\models\PurchaseProvider;
use app\models\StockOut;
use app\models\StockOutDetail;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\data\Pagination;

class StockOutForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $user;
    public $type;
    public $create_time;
    public $startDate;
    public $endDate;
    public $status_text = [
        0 => '待出库',
        1 => '已出库',
    ];
    public $type_text = [
        1 => '销售出库',
        2 => '其它出库',
        3 => '调货出库',
        4 => '采购退货',
        5 => '规格转换',
        6 => '报损出库'
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'integer',],
            [['type',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询出库单列表
     * @return array
     */
    public function search($filterProperty)
    {

        $query = StockOut::find();

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty, true);
            $startDate = isset($json['startDate']) ? $json['startDate'] : null;
            if ($startDate != null) {
                $query->andWhere([
                    'and',
                    ['>=', 'out_time', $startDate . ' 00:00:00'],
                    ['<=', 'out_time', $json['endDate'] . ' 23:59:59'],
                ]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['or', ['like', 'out_no', "%$searchText%", false], ['like', 'user_name', "%$searchText%", false]]);
            }
            $status = isset($json['status']) ? $json['status'] : null;
            if ($status != null) {
                $query->andWhere(['status' => $status]);
            }
            $type = isset($json['type']) ? $json['type'] : null;
            if ($type != null) {
                $query->andWhere(['type' => $type]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as $k => &$v) {
            $v['status_name'] = $this->status_text[$v['status']];
            $v['type_name'] = isset($this->type_text[$v['type']]) ? $this->type_text[$v['type']] : '';
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 创建入出库单
     * @param $post
     * @return mixed
     */
    public function createStock($post)
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $model = ($id) ? StockOut::findOne($post['id']) : new StockOut();
        $model->attributes = $post;//属性赋值
        if (!$id) {
            $model->out_no = $this->getNo();
        }
        $model->type_name = isset($model->type) ? $this->type_text[$model->type] : '';
        $model = $this->initData($model);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $model->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$model->validate() || !$model->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $model->getErrors()];
        }
        $model->total_price = $this->getSummary($model->commodity_list);
        if ($res = $model->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($model->commodity_list, $model, $id);
            if ($res_detail['code'] == 400) {
                $t->rollBack();
                return ['code' => 400, 'msg' => '商品数据保存失败', 'data' => $res_detail['data']];
            }
            //更新商品库存数量
            $this->updateCommodityStockNum($model->commodity_list);
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $t->commit();//提交事务
        return $res;
    }


    /**
     * 更新商品库存数量
     * @param $data
     * @return bool
     */
    /**
     * 更新商品库存数量
     * @param $data
     * @return bool
     */
    public function updateCommodityStockNum($data)
    {
        foreach ($data as $v) {
            $res = CommodityProfile::updateAllCounters(['stock_num' => (-1) * $v['num']], ['commodity_id' => $v['commodity_id'], 'name' => $v['unit']]);
            //更新库存
            if (!$res) {
                return false;
            }
        }
        return true;
    }

    /**
     * 更新出库单状态
     * @param $purchase_id
     * @param $status
     * @return mixed
     */
    public function updateStatus($id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = StockOut::findOne($id);
            $model->status = $status;
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

    // 保存出库单根据订单list
    public function saveByOrderList($orderList)
    {
        foreach ($orderList as $item) {
            $this->saveByOrderOne($item);
        }
    }

    // 保存出库单根据订单One
    public function saveByOrderOne($order)
    {
        $stockOut = new StockOut();
        $orderDetail = OrderDetail::find()->asArray()
            ->where(['order_id' => $order['id']])
            ->all();
        $this->initDataByOrder($stockOut, $order, 1, 1, $orderDetail);
        if (!$stockOut->save()) {
            throw new Exception($stockOut->errors);
        }

        // 如果未付款生成客户对账单 已付款完成多退少补并添加结算单
        if ($order['is_pay'] == 'N') {
            $userAudit = new UserAuditForm();
            $userAudit->createUserAuditByOrder($order, $orderDetail, $stockOut->total_price);
        } else {
            $finance = new FinanceForm();
            $finance->createByOrder($order, $orderDetail);
        }

        // 保存出库单子表
        foreach ($orderDetail as $item) {
            $this->saveDetailByOrderDetail($item, $stockOut);
        }
        // 更新库存
        $this->updateCommodityStockNum($orderDetail);
    }

    // 保存出库单明细
    private function saveDetailByOrderDetail($orderDetail, $stockOut)
    {
        $stockOutDetail = new StockOutDetail();
        $stockOutDetail->out_id = $stockOut->id;
        $stockOutDetail->out_no = $stockOut->out_no;
        $stockOutDetail->commodity_id = $orderDetail['commodity_id'];
        $stockOutDetail->commodity_name = $orderDetail['commodity_name'];
        $stockOutDetail->pic = $orderDetail['pic'];
        $stockOutDetail->price = $orderDetail['price'];
        $stockOutDetail->num = $orderDetail['actual_num'];
        $stockOutDetail->unit = $orderDetail['unit'];
        $stockOutDetail->total_price = $orderDetail['actual_num'] * $orderDetail['price'];
        $stockOutDetail->actual_num = $orderDetail['actual_num'];
        $stockOutDetail->is_basics_unit = $orderDetail['is_basics_unit'];
        $stockOutDetail->base_self_ratio = $orderDetail['base_self_ratio'];
        if (!$stockOutDetail->save()) {
            $errors = $stockOutDetail->errors;
            throw new Exception('出库单明细保存失败');
        }
    }

    /**
     * 保存出库单
     * @return array
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $model = ($id) ? StockOut::findOne($post['id']) : new StockOut();
        $model->attributes = $post;//属性赋值
        $model->out_no = $this->getNo();
        $model->out_time = date('Y-m-d H-i-s', time());
        $model = $this->initData($model);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $model->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$model->validate() || !$model->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $model->getErrors()];
        }
        $model->total_price = $this->getSummary($model->commodity_list);
        if ($res = $model->save()) {
            //保存订单商品数据
            $res_detail = $this->insertData($model->commodity_list, $model, $id);
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
     * 初始化出库单数据
     * @param $model
     * @return mixed
     */
    private function initData($model)
    {
        //$status = 1;//已入库
        $model->out_no = $this->getNo();
        $model->operator = Yii::$app->user->identity['username'];
        $model->op_id = Yii::$app->user->identity['id'];
        $model->create_time = time();
        return $model;
    }

    /**
     * 初始化出库单数据
     * @param $model
     * @return mixed
     */
    private function initDataByOrder(&$model, $order, $status, $type, $modelDetail)
    {
        $model->out_no = $this->getNo();
        $model->status = $status;
        $model->status_name = $this->status_text[$status];
        $model->type = $type;
        $model->type_name = $this->type_text[$type];
        $model->operator = Yii::$app->user->identity['username'];
        $model->op_id = Yii::$app->user->identity['id'];
        $model->create_time = time();
        $model->about_id = $order['id'];
        $model->about_no = $order['order_no'];
        $model->user_id = $order['user_id'];
        $model->user_name = $order['user_name'];
        $model->out_time = date('Y-m-d H:i:s', time());
        $this->getNumAndTotalPrice($model, $modelDetail);
        return $model;
    }

    // 计算数量跟总价
    private function getNumAndTotalPrice(&$model, $modelDetail)
    {
        $totalPrice = 0;
        foreach ($modelDetail as $item) {
            $totalPrice += $item['price'] * $item['actual_num'];
        }

        $model->num = count($modelDetail);
        $model->total_price = $totalPrice;
    }

    /**
     * 计算入库单总额
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
     * 插入出库单商品数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $model, $id = 0)
    {
        $detail = new StockOutDetail();
        $b = \Yii::$app->db->beginTransaction();
        $detail->updateAll(['is_delete' => 1], ['out_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = isset($attribute['name'])?$attribute['name']:$attribute['commodity_name'];
            $_model = clone $detail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'num' => $attribute['num'],
                    'price' => $attribute['price'],
                    'total_price' => $attribute['num'] * $attribute['price'],
                    'is_delete' => 0,
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->out_id = $model->id;
            $_model->total_price = $_model->price * $_model->num;
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
     * 生成出库单号
     * @return null|string
     */
    public function getNo()
    {
        $no = null;
        while (true) {
            $no = 'CK' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_no = StockOut::find()->where(['out_no' => $no])->exists();
            if (!$exist_no) {
                break;
            }
        }
        return $no;
    }


    /**
     * 获取打印数据
     * @return array
     */
    public function getPrintData($id)
    {
        $query = StockOut::find()->where(['id' => $id])->one();
        $data = $query->toArray();
        $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
        return ['item' => $query->getDetails()->asArray()->all(), 'order' => $data];
    }

    // 根据商品的出库查询
    public function stockOutByCommodityData($filterProperty)
    {
        $query = StockOutDetail::find()
            ->select('bn_stock_out_detail.*, bn_stock_out.type_name, bn_stock_out.about_no, bn_stock_out.out_time')
            ->leftJoin('bn_stock_out', 'bn_stock_out.id = bn_stock_out_detail.out_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty, true);
            $startDate = isset($json['startDate']) ? $json['startDate'] : null;
            if ($startDate != null) {
                $query->andWhere([
                    'and',
                    ['>=', 'out_time', $startDate . ' 00:00:00'],
                    ['<=', 'out_time', $json['endDate'] . ' 23:59:59'],
                ]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['or', ['like', 'bn_stock_out_detail.commodity_name', "%$searchText%", false]]);
            }
            $status = isset($json['status']) ? $json['status'] : null;
            if ($status != null) {
                $query->andWhere(['status' => $status]);
            }
            $type = isset($json['type']) ? $json['type'] : null;
            if ($type != null) {
                $query->andWhere(['type' => $type]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return ['total' => $count, 'list' => $list];
    }

    /**
     * 审核出库单
     * @param $id
     * @return bool
     */
    public function audit($id)
    {
        $stockOut = StockOut::findOne($id);
        if ($stockOut->status === 1) {
            return false;
        }
        $model = StockOutDetail::find()->where(['out_id' => $id])->asArray()->all();
        if ($this->updateCommodityStockNum($model)) {
            $stockOut->status = 1;
            //$stockOut->out_time = date("Y-m-d H:i:s",time());
            if ($stockOut->save()){
                return true;
            }
            else{
                var_dump($stockOut->getErrors());
            }
        }
        return false;
    }

}
