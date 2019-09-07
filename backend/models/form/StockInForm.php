<?php

namespace app\models\form;

use app\models\CommodityProfile;
use app\models\Model;
use app\models\StockIn;
use app\models\StockInDetail;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;

class StockInForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $user;
    public $type;
    public $create_time;
    public $type_first_tier_id;
    public $in_time;
    public $status_text = [
        0 => '待入库',
        1 => '已入库',
    ];
    public $type_text = [
        1 => '采购入库',
        2 => '其它入库',
        3 => '调货入库',
        4 => '订单入库',
        5 => '规格转换',
        6 => '期初入库',
        7 => '报溢入库',
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'integer'],
            [['type',], 'default', 'value' => ''],
            [['create_time',], 'default', 'value' => ''],
            [['type_first_tier_id',], 'default', 'value' => ''],
            [['in_time',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询入库单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = StockIn::find();
        $query->andWhere(['is_delete' => 0]);
        if ($this->status === '0' || $this->status === '1') {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'in_no', $this->keyword],
            ]);
        }
        if ($this->type) {
            $query->andWhere([
                'type' => $this->type
            ]);
        }
        if ($this->in_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'in_time',
                    $this->dateFormat($this->in_time)['begin_date']
                ],
                [
                    '<=', 'in_time',
                    $this->dateFormat($this->in_time)['end_date']
                ],
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
            $v['status_name'] = $this->status_text[$v['status']];
            $v['type_name'] = isset($this->type_text[$v['type']]) ? $this->type_text[$v['type']] : '';
            $v['in_time'] = $v['in_time'] ? date("Y-m-d H:i:s", $v['in_time']) : 0;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 查询入库单列表
     * @return array
     */
    public function commoditySearch()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = StockInDetail::find();
        $query->where(['bn_stock_in.status' => 1]);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'bn_stock_in_detail.in_no', $this->keyword],
                ['like', 'commodity_name', $this->keyword],
            ]);
        }
        if ($this->type_first_tier_id) {
            $query->andWhere([
                'type_first_tier_id' => $this->type_first_tier_id
            ]);
        }
        if ($this->type) {
            $query->andWhere([
                'bn_stock_in.type' => $this->type
            ]);
        }
        if ($this->in_time) {
            $query->andwhere([
                'and',
                [
                    '>=', 'in_time',
                    $this->dateFormat($this->in_time)['begin_date']
                ],
                [
                    '<=', 'in_time',
                    $this->dateFormat($this->in_time)['end_date']
                ],
            ]);
        }
        $query->select('status,bn_stock_in_detail.id,bn_stock_in.in_no,bn_stock_in.type,bn_stock_in.type_name,store_id_name,in_time,commodity_name,price,bn_stock_in_detail.total_price,unit,num');
        $query->leftJoin('bn_stock_in', 'bn_stock_in.id=bn_stock_in_detail.in_id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('in_time DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as $k => &$v) {
            $v['in_time'] = $v['in_time'] ? date("Y-m-d H:i:s", $v['in_time']) : 0;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 更新入库单状态
     * @param $purchase_id
     * @param $status
     * @return mixed
     */
    public function updateStatus($id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = StockIn::findOne($id);
            $model->status = 4;
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
            'end_date' => $timestamp ? strtotime($date_interval[1]) + 3600 * 24 : $date_interval[1]
        ];
    }


    /**
     * 保存入库单
     */
    public function save()
    {
        $post = Yii::$app->request->post();
        return $this->createStock($post);
    }


    /**
     * 创建入库单
     * @param $post
     * @return mixed
     */
    public function createStock($post)
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $model = ($id) ? StockIn::findOne($post['id']) : new StockIn();
        $model->attributes = $post;//属性赋值
        $model->in_no = $this->getNo();
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
        } else {
            $t->rollBack();
            return ['code' => 400, 'msg' => '数据保存失败', 'data' => []];
        }
        $t->commit();//提交事务
        return $res;
    }


    /**
     * 初始化入库单数据
     * @param $model
     * @return mixed
     */
    private function initData($model)
    {
        $model->in_no = $this->getNo();
        $model->operator = Yii::$app->user->identity['username'];
        $model->op_id = Yii::$app->user->identity['id'];
        $model->create_time = time();
        return $model;
    }

    /**
     * 更新商品库存数量
     * @param $data
     * @return bool
     */
    public function updateCommodityStockNum($data)
    {
        foreach ($data as $v) {
            $res = CommodityProfile::updateAllCounters(['stock_num' => $v['num']], ['commodity_id' => $v['commodity_id'],'name'=>$v['unit']]);
            //更新库存
            if (!$res) {
                return false;
            }
        }
        return true;
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
     * 插入入库单商品数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $model, $id = 0)
    {
        $detail = new StockInDetail();
        $b = \Yii::$app->db->beginTransaction();
        $detail->updateAll(['is_delete' => 1], ['in_id' => $id]);
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
            $_model->in_id = $model->id;
            $_model->in_no = $model->in_no;
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
     * 生成入库单号
     * @return null|string
     */
    public function getNo()
    {
        $no = null;
        while (true) {
            $no = 'RK' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_no = StockIn::find()->where(['in_no' => $no])->exists();
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
        $query = StockIn::find()->where(['id' => $id])->one();
        $data = $query->toArray();
        $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
        return ['item' => $query->getDetails()->asArray()->all(), 'order' => $data];
    }


    /**
     * 审核入库单
     * @param $id
     * @return bool
     */
    public function audit($id)
    {
        $stockIn = StockIn::findOne($id);
        if ($stockIn->status === 1) {
            return false;
        }
        $model = StockInDetail::find()->where(['in_id' => $id])->asArray()->all();
        if ($this->updateCommodityStockNum($model)) {
            $stockIn->status = 1;
            $stockIn->in_time = time();
            if ($stockIn->save())
                return true;
        }
        return false;
    }

}
