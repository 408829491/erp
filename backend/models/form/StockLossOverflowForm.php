<?php

namespace app\models\form;

use app\models\Model;
use app\models\Purchase;
use app\models\PurchaseBuyer;
use app\models\PurchaseDetail;
use app\models\PurchaseProvider;
use app\models\StockLossOverflow;
use app\models\StockLossOverflowDetail;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;

class StockLossOverflowForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $type;
    public $user;
    public $create_time;
    public $status_text = [
        0 => '待审核',
        1 => '已审核',
    ];
    public $type_text = [
        1 => '报损',
        2 => '报溢',
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
        ];
    }

    /**
     * 查询列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = StockLossOverflow::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'no', $this->keyword],
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
            $v['check_time'] = $v['check_time']?date("Y-m-d H:i:s",$v['check_time']):'无';
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 更新状态
     * @param $id
     * @param $status
     * @return array|bool
     * @throws ErrorException
     */
    public function updateStatus($id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = StockLossOverflow::findOne($id);
            $model->status = $status;
            $model->check_time = time();
            if ($model->save()) {
                //生成出入库单
                $this->createStock($id);
                return true;
            }
        } else {
            throw new ErrorException('状态值不合法', 400);
        }
        return $model->getErrors();
    }


    /**
     * 创建报损报溢出入库单
     * @param $id
     */
    public function createStock($id){
        $StockLossOverflow = StockLossOverflow::find()->with('details')->asArray()->where(['id' => $id])->one();
        $type = $StockLossOverflow['type'];
        if($type==='1'){
            $model = new StockOutForm();
            $stock_type  = 6;//报损出库
            $post['out_time'] = date("Y-m-d H:i:s",time());
        }else{
            $model = new StockInForm();
            $stock_type  = 7;//报溢入库
            $post['in_time'] = time();

        }
        $details = $StockLossOverflow['details'];
        $post['about_no'] = $StockLossOverflow['no'];
        $post['store_id_name'] = '默认仓库';
        $post['type'] = $stock_type;//入库类型：采购入库
        $post['status'] = 1; //入库状态：已入库
        $post['commodity_list'] = $details;
        if($model->createStock($post)){
            $model->updateCommodityStockNum($details);
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
     * 保存
     * @return array
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $model = ($id) ? StockLossOverflow::findOne($post['id']) : new StockLossOverflow();
        $model->attributes = $post;//属性赋值
        $model->no = $this->getNo($model->type);
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
     * 初始化数据
     * @param $model
     * @return mixed
     */
    private function initData($model)
    {
        $status = 0;
        $model->status = $status;
        $model->create_user = Yii::$app->user->identity['username'];
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算总额
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
     * 插入商品数据
     * @param $commodity_list商品数据
     * @return array
     */
    private function insertData($commodity_list, $model, $id = 0)
    {
        $detail = new StockLossOverflowDetail();
        $b = \Yii::$app->db->beginTransaction();
        $detail->updateAll(['is_delete' => 1], ['loss_over_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $detail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'num' => $attribute['num'],
                    'total_price' => $attribute['num'] * $attribute['price'],
                    'remark' => $attribute['remark'],
                    'is_delete' => 0,
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->loss_over_id = $model->id;
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
     * 生成单号
     * @return null|string
     */
    public function getNo($type)
    {
        $no = null;
        while (true) {
            $prefix=($type==1)?'BS':'BY';
            $no = $prefix . date('YmdHis') . mt_rand(10000, 99999);
            $exist_no = StockLossOverflow::find()->where(['no' => $no])->exists();
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
        $query = StockLossOverflow::find()->where(['id' => $id])->one();
        $data = $query->toArray();
        $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
        return ['item' => $query->getDetails()->asArray()->all(), 'order' => $data];
    }

}
