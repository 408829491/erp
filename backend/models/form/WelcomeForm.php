<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/8/22
 * Time: 13:14
 */

namespace app\models\form;


use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use common\models\User;

class WelcomeForm extends Model
{
    /**
     * 获取首页统计数据
     * @return array
     */
    public function getIndexStatistic()
    {
        $week = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");
        //订单总量、总营业额
        $order = Order::find()->select(['count(id) as total_orders', 'SUM(IF(DATE_FORMAT(FROM_UNIXTIME(create_time),"%Y-%m-%d")=DATE_FORMAT(NOW(),\'%Y-%m-%d\'),1,0)) AS total_today_orders', 'sum(price) as total_prices', 'SUM(IF(DATE_FORMAT(FROM_UNIXTIME(create_time),"%Y-%m-%d")=DATE_FORMAT(NOW(),\'%Y-%m-%d\'),price,0)) AS total_today_prices'])
            ->asArray()
            ->where(['<>', 'status', 4])
            ->one();
        //客户总量
        $users = User::find()->select(['count(id) as total_users', 'SUM(IF(DATE_FORMAT(FROM_UNIXTIME(created_at),"%Y-%m-%d")=DATE_FORMAT(NOW(),\'%Y-%m-%d\'),1,0)) AS total_today_users'])
            ->asArray()
            ->where(['<>', 'is_deleted', 1])
            ->one();
        //营业数据趋势
        $business = Order::find()
            ->select('FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,FROM_UNIXTIME(create_time,("%Y-%m-%d")) as date,count(id) as order_count,sum(quantity) as commodity_count,sum(price) as total_price,sum(total_profit) as total_profit,(FROM_UNIXTIME(create_time,("%w"))) as week')
            ->asArray()
            ->groupBy('date')
            ->all();
        foreach ($business as $k => &$v) {
            $v['id'] = $k + 1;
            $v['date'] = $v['date'] . ' (' . $week[$v['week']] . ')';
            $v['total_profit_radio'] = round(($v['total_profit'] / $v['total_price'] * 100), 2) . '%';
        }

        //大类排行
        $orderCategory = OrderDetail::find()
            ->select('sum(num) as total_count,bn_commodity_category.name')
            ->where(['bn_order_detail.is_delete' => 0])
            ->leftJoin('bn_commodity_category', 'bn_commodity_category.id=bn_order_detail.type_id')
            ->groupBy('type_first_tier_id')
            ->orderBy('total_count desc')
            ->asArray()
            ->limit(7)
            ->all();
        //商品排行
        $orderDetail = OrderDetail::find()
            ->select('commodity_name,sum(num) as total_count,bn_commodity_category.name')
            ->where(['bn_order_detail.is_delete' => 0])
            ->leftJoin('bn_commodity_category', 'bn_commodity_category.id=bn_order_detail.type_id')
            ->groupBy('commodity_id')
            ->orderBy('total_count desc')
            ->asArray()
            ->limit(7)
            ->all();
        //客户排行
        $orderUser = Order::find()
            ->select('nick_name,count(id) as total_count,sum(price) as total_price')
            ->where(['<>', 'status', 4])
            ->groupBy('user_id')
            ->orderBy('total_count desc')
            ->asArray()
            ->limit(7)
            ->all();
        return [
            'order' => $order,
            'users' => $users,
            'business' => $business,
            'orderDetail' => $orderDetail,
            'orderUser' => $orderUser,
            'orderCategory' => $orderCategory
        ];
    }

}