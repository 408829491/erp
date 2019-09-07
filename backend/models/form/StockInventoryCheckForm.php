<?php

namespace app\models\form;

use app\models\Model;
use app\models\Purchase;
use app\models\PurchaseBuyer;
use app\models\PurchaseDetail;
use app\models\PurchaseProvider;
use app\models\StockInventoryCheck;
use app\models\StockInventoryCheckDetail;
use Yii;
use yii\base\ErrorException;
use yii\data\Pagination;

class StockInventoryCheckForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $user;
    public $type;
    public $create_time;
    public $status_text = [
        0 => '待审核',
        1 => '已审核',
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
     * 查询盘点单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = StockInventoryCheck::find();
        if ($this->status) {
            $query->andWhere([
                'status' => $this->status
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'check_no', $this->keyword],
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
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 更新盘点单状态
     * @param $id
     * @param $status
     * @return array|bool
     * @throws ErrorException
     */
    public function updateStatus($id, $status)
    {
        if (in_array($status, array_keys($this->status_text))) {
            $model = StockInventoryCheck::findOne($id);
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


    /**
     * 保存盘点单
     * @return array
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $this->getErrors()];
        }
        $post = Yii::$app->request->post();
        $id = isset($post['id']) ? (int)$post['id'] : 0;
        $model = ($id) ? StockInventoryCheck::findOne($post['id']) : new StockInventoryCheck();
        $model->attributes = $post;//属性赋值
        $model->check_no = $this->getNo();
        $model = $this->initData($model);
        $t = \Yii::$app->db->beginTransaction();//开始事务
        $model->commodity_list = isset($post['commodity_list']) ? $post['commodity_list'] : [];
        if (!$model->validate() || !$model->commodity_list) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $model->getErrors()];
        }
        $model->overflow_price = $this->getSummary($model->commodity_list)['overflow_price'];
        $model->loss_price = $this->getSummary($model->commodity_list)['loss_price'];
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
     * 初始化盘点单数据
     * @param $model
     * @return mixed
     */
    private function initData($model)
    {
        $status = 0;
        $model->check_no = $this->getNo();
        $model->status = $status;
        $model->operator = Yii::$app->user->identity['username'];
        $model->op_id = Yii::$app->user->identity['id'];
        $model->create_time = time();
        return $model;
    }


    /**
     * 计算盘点单总额
     * @param array $commodity_list
     * @return mixed
     */
    private function getSummary($commodity_list = [])
    {
        $loss_price = $overflow_price = 0;
        foreach ($commodity_list as $v) {
            $loss_price += $v['price'] * $v['sell_stock'];
            $overflow_price += $v['price'] * $v['num'];
        }
        return ['loss_price' => $loss_price, 'overflow_price' => $overflow_price];
    }

    /**
     * 插入盘点单商品数据
     * @param $commodity_list
     * @param $model
     * @param int $id
     * @return array
     * @throws \yii\db\Exception
     */
    private function insertData($commodity_list, $model, $id = 0)
    {
        $detail = new StockInventoryCheckDetail();
        $b = \Yii::$app->db->beginTransaction();
        $detail->updateAll(['is_delete' => 1], ['check_id' => $id]);
        foreach ($commodity_list as $attribute) {
            $attribute['commodity_name'] = $attribute['name'];
            $_model = clone $detail; //克隆对象,防止只插入一条数据
            if ($id) {//更新已有记录
                $data = [
                    'commodity_name' => $attribute['commodity_name'],
                    'num' => $attribute['num'],
                    'price' => $attribute['price'],
                    'remark' => $attribute['remark'],
                    'is_delete' => 0,
                ];
                if ($_model->updateAll($data, ['id' => $attribute['id']])) {
                    continue;
                }
            }
            $_model->setAttributes($attribute);
            $_model->create_time = time();
            $_model->check_id = $model->id;
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
     * 生成盘点单号
     * @return null|string
     */
    public function getNo()
    {
        $no = null;
        while (true) {
            $no = 'PD' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_no = StockInventoryCheck::find()->where(['check_no' => $no])->exists();
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
        $query = StockInventoryCheck::find()->where(['id' => $id])->one();
        $data = $query->toArray();
        $data['create_time'] = date("Y-m-d H:i:s", $data['create_time']);
        return ['item' => $query->getDetails()->asArray()->all(), 'order' => $data];
    }


}
