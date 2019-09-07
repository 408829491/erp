<?php

namespace app\models\form;

use app\models\CusOrder;
use app\models\Order;
use Yii;
use yii\base\Model;
use yii\data\Pagination;

class DeliveryTaskForm extends Model
{
    const C_MANAGER = 'C端管理员';
    const C_DRIVER = '门店配送员';
    const B_DRIVER = '配送员';
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $lat;
    public $lng;
    public $create_time;
    public $begin_date;
    public $end_date;
    public $user;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['lng',], 'default', 'value' => 0,],
            [['lat',], 'default', 'value' => 0,],
            [['begin_date',], 'default', 'value' => 0,],
            [['end_date',], 'default', 'value' => 0,],
            [['create_time',], 'default', 'value' => ''],
        ];
    }

    /**
     * 统计配送任务信息
     * @return array
     */
    public function getTaskList()
    {
        $this->attributes = Yii::$app->request->post();
        if (!$this->validate()) {
            return [];
        }
        $deliveryId = $this->user['id'];
        $date = date('Y-m-d', time());
        //获取当前用户角色
        $role = $this->getRole($this->user['id']);
        $c_type = 0;
        if ($role === self::C_DRIVER) {
            $query = CusOrder::find();
            $c_type = 1;
            $status = 2; //默认状态
        } else {
            $query = Order::find();
            $status = 1; //默认状态
        }
        if($this->status == 2){
            $status = 3;
        }
        $query->where(['driver_id' => $deliveryId, 'status' => $status]);
        if (($this->begin_date) && ($this->end_date) && ($this->status == '2')) {
            $query->andWhere(['>=', 'achieve_date', (int)$this->begin_date]);
            $query->andWhere(['<=', 'achieve_date', (int)$this->end_date]);
        }
        if ($this->status == 1 ) {
            $query->andWhere(['exception_status' => 1]);
        } else {
            $query->andWhere(['exception_status' => 0]);
        }
        $query->andWhere(['<=', 'delivery_date', $date]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->select('id,order_no,receive_name,receive_name,receive_tel,delivery_date,delivery_time_detail
                              ,address_lng,address_lat,address_detail,address_name,achieve_date')
            ->offset($pagination->offset)
            ->orderBy('user_id DESC,id desc')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as &$value) {
            //计算距离
            $value['distance'] = self::getDistance($this->lng, $this->lat, $value['address_lng'], $value['address_lat']);
            $value['achieve_date'] = date("Y-m-d H:i:s", $value['achieve_date']);
            if ($c_type == 1) {
                $value['address_detail'] = $value['address_name'] . ' ' . $value['address_detail'];
                unset($value['address_name']);
            }
        }
        return [
            'total' => $count,
            'deduction' => round($count * 10, 2),
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql(),
        ];
    }

    /**
     * 配送异常提报
     * @return array
     */
    public function reportOrderException($id, $status = 0)
    {
        if ($this->getRole($this->user['id']) === self::B_DRIVER) {
            $model = new OrderForm();
        } else {
            $model = new CusOrderForm();
        }
        if ($model->updateOrderExceptionStatus($id, $status)) {
            return ['code' => '200', 'msg' => 'ok', 'data' => []];
        }
        return ['code' => '400', 'msg' => 'false', 'data' => []];
    }


    /**
     * 确认送达
     * @return array
     */
    public function confirmDelivery($id)
    {
        $status = 3;//商品送达状态
        if ($this->getRole($this->user['id']) === self::B_DRIVER) {
            $model = new OrderForm();
        } else {
            $model = new CusOrderForm();
        }
        if ($model->updateOrderStatus($id, $status)) {
            return ['code' => '200', 'msg' => 'ok', 'data' => []];
        }
        return ['code' => '400', 'msg' => 'false', 'data' => []];
    }

    /**
     * 获取格式化距离
     * @param $lng ：用户经度
     * @param $lat ：用户纬度
     * @param $c_lng ：目标点经度
     * @param $c_lat ：目标点纬度
     * @return int|string
     */
    public static function getDistance($lng, $lat, $c_lng, $c_lat)
    {
        $distance = \app\utils\GeoDeUtils::calculateDistance($lng, $lat, $c_lng, $c_lat);
        $distance = ($distance >= 1000) ? round($distance / 1000, 2) . 'km' : ceil($distance) . 'm';
        return $distance;
    }


    /**
     * 获取用户角色
     * @param $userId
     * @return string
     */
    public function getRole($userId)
    {
        $auth = Yii::$app->authManager;
        //获取当前用户角色
        $role = array_keys($auth->getRolesByUser($userId));
        if (in_array(self::C_DRIVER, $role)) {
            return self::C_DRIVER;
        } else if (in_array(self::C_MANAGER, $role)) {
            return self::C_MANAGER;
        } else {
            return self::B_DRIVER;
        }

    }


    /**
     * 统计配送状态数量
     * @return mixed
     */
    public function getOrderStatistic()
    {
        if ($this->getRole($this->user['id']) === self::B_DRIVER) {
            $query = Order::find();
            $status = 1;
        } else {
            $query = CusOrder::find();
            $status = 2;
        }
        $query->where(['status' => $status, 'driver_id' => $this->user['id']]);
        $date = date('Y-m-d', time());
        $query->andWhere(['<=', 'delivery_date', $date]);
        $data = $query->select([
            'SUM( IF(exception_status = "1", 1, 0) ) AS total_exception',
            'SUM( IF(exception_status = "0", 1, 0) ) AS total_normal',
            'SUM( IF(exception_status = "2", 1, 0) ) AS total_achieve',
        ])
            ->asArray()
            ->one();
        $data['total_exception'] = (null === $data['total_exception']) ? 0 : $data['total_exception'];
        $data['total_normal'] = (null === $data['total_normal']) ? 0 : $data['total_normal'];
        $data['total_achieve'] = (null === $data['total_achieve']) ? 0 : $data['total_achieve'];
        $data['sql'] = $query->createCommand()->getRawSql();
        return $data;
    }
}
