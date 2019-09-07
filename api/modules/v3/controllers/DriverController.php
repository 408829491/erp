<?php

namespace api\modules\v3\controllers;

use app\models\CusOrder;
use app\models\CusStore;
use app\models\Order;
use app\utils\GeoDeUtils;
use Yii;

/**
 * 配送员Controller
 * Class DriverController
 * @package api\modules\v3\controllers
 */
class DriverController extends Controller
{
    const C_MANAGER = 'C端管理员';
    const C_DRIVER = '门店配送员';
    const B_DRIVER = '配送员';

    // 查询出所有订单，并规划路线
    public function actionOrderListPlan() {
        $lng = \Yii::$app->request->post("lng");
        $lat = \Yii::$app->request->post("lat");

        $role = $this->getRole($this->user->id);
        if ($role == self::C_DRIVER) {
            $order = CusOrder::find()->asArray()
                ->select('address_lng, address_lat, address_name')
                ->where(["driver_id" => $this->user->id])
                ->andWhere(['status' => 2])
                ->andWhere(['is_send_to_home' => 1])
                ->andWhere(['exception_status' => 0])
                ->andWhere(['<=', 'delivery_date', date('Y-m-d', time())])
                ->all();
        } else if ($role == self::B_DRIVER) {
            $order = Order::find()->asArray()
                ->select('address_lng, address_lat, receive_name')
                ->where(["driver_id" => $this->user->id])
                ->andWhere(['status' => 1])
                ->andWhere(['exception_status' => 0])
                ->andWhere(['<=', 'delivery_date', date('Y-m-d', time())])
                ->all();
        }

        // 计算出最近的距离,并且排序
        $result = [];
        while (count($order) != 0) {
            $index = $this->fastPath([$lng, $lat], $order);
            // 规划路线
            $tempOrder = $order[$index];
            $drivingResult = GeoDeUtils::directionDriving($lng, $lat, $tempOrder['address_lng'], $tempOrder['address_lat']);
            if ($drivingResult['status'] == 1) {
                $tempOrder['drivingResult'] = $drivingResult['route'];
            }
            array_push($result, $tempOrder);
            $lng = $tempOrder['address_lng'];
            $lat = $tempOrder['address_lat'];
            array_splice($order, $index, 1);
        }

        return ['orderList' => $result, 'store' => CusStore::findOne($this->user->store_id)];
    }

    // 从列表中取出最近的一个
    private function fastPath($points, $lists) {
        // 计算距离
        $index = 0;
        $distance = GeoDeUtils::calculateDistance($points[0], $points[1], $lists[0]['address_lng'], $lists[0]['address_lat']);

        for ($i = 1; $i < count($lists); $i++) {
            $tempDistance = GeoDeUtils::calculateDistance($points[0], $points[1], $lists[$i]['address_lng'], $lists[$i]['address_lat']);
            if ($tempDistance < $distance) {
                $distance = $tempDistance;
                $index = $i;
            }
        }

        return $index;
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
}