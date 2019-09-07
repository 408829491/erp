<?php

namespace app\models\form;

use app\models\CusOrderDetail;
use app\models\CusShopcart;
use Yii;
use yii\base\Exception;

class CusShopcartForm extends \yii\base\Model
{
    // 保存购物车
    public function save($data, $storeId, $userId)
    {
        // 删除原有的购物车数据，保存新的
        $transaction  = CusShopcart::getDb()->beginTransaction();

        try {
            CusShopcart::deleteAll(['store_id' => $storeId, 'cus_member_id' => $userId]);
            foreach ($data as $item) {
                $cusShopCart = new CusShopcart();
                $cusShopCart->attributes = $item;
                $cusShopCart->id = $item['id'];
                $cusShopCart->save();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 清除购物车
    public function clearShopCart($orderId){
         $order = Yii::$app->db
             ->createCommand()
             ->delete(CusShopcart::tableName(),'id in (select shop_cart_id from '.CusOrderDetail::tableName().' where order_id='.$orderId.')')
             ->execute();
         return $order;
    }
}