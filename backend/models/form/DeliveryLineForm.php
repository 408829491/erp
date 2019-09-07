<?php

namespace app\models\form;

use app\models\Customer;
use app\models\DeliveryDriver;
use app\models\DeliveryLine;
use app\models\Model;
use app\models\Order;
use app\models\Salesman;
use common\models\User;
use Yii;
use yii\data\Pagination;

class DeliveryLineForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $area_name;
    public $create_time;
    public $password;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['area_name',], 'default', 'value' => '',],
            [['create_time',], 'default', 'value' => ''],
            [['password',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询线路列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = DeliveryLine::find()
            ->where(['<>', 'is_delete', '1']);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'name', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        $line_statistic = $this->getLineOrderInfo(); //统计线路订单信息
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['total_user'] = isset($line_statistic[$v['id']]) ? $line_statistic[$v['id']]['total_user'] : 0;
            $v['total_order_count'] = isset($line_statistic[$v['id']]) ? $line_statistic[$v['id']]['total_order_count'] : 0;
            $v['total_price'] = isset($line_statistic[$v['id']]) ? $line_statistic[$v['id']]['total_price'] : 0;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 获取线路订单统计信息
     * @return array
     */
    public function getLineOrderInfo()
    {
        $query = Order::find()
            ->select([
                'line_id',
                'line_name',
                'count(user_id) AS total_user',
                'count(id) AS total_order_count',
                'SUM(price) AS total_price',
            ])
            ->asArray()
            ->groupBy('line_id')
            ->all();
        $data = [];
        foreach ($query as $k => $v) {
            $data[$v['line_id']] = $v;
        }
        return $data;
    }

    /**
     * 删除线路
     * @param $id
     */
    public function delete($id)
    {
        $model = DeliveryLine::findOne($id);
        $model->is_delete = 1;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 保存/新增线路信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0)
    {
        $model = $id ? DeliveryLine::findOne($id) : new DeliveryLine();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        $driver = DeliveryDriver::findOne(['id' => $model->driver_id]);
        $model->driver_name = $driver->nickname;
        $model->driver_tel = $driver->mobile;
        if (!$id) {
            $model->create_time = time();
        }
        if ($model->save()) {
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }

    /**
     * 获取线路列表
     * @return mixed
     */
    public function getLineList()
    {
        $gps = new GpsForm();
        $gpsData = array_column($gps->getGpsData()['data'],NUll,'imei');
        $model = DeliveryLine::find();
        $query = $model
            ->where(['is_delete'=>0])
            ->asArray()
            ->all();
        foreach($query as &$v){
            $v['lat'] = $gpsData[$v['gps_imei']]['lat'];
            $v['lng'] = $gpsData[$v['gps_imei']]['lng'];
        }
        return [
            'list' => $query
        ];
    }


}
