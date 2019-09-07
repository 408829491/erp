<?php

namespace app\models\form;

use app\models\FinanceAccountSettle;
use app\models\FinanceAccountSettleDetail;
use app\models\Model;
use app\models\UserAudit;
use app\models\UserAuditDetail;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * 客户对账Form
 * Class StockOutForm
 * @package app\models\form
 */
class UserAuditForm extends Model
{
    // 查询分页列表
    public function findPage($pageNum, $pageSize, $filterProperty) {
        $query = UserAudit::find()
            ->select(['bn_user_audit.*', 'round((audit_price) - (received_price), 2) as unPayPrice']);

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $startDate = isset($json['startDate']) ? $json['startDate'] : null;
            if ($startDate != null) {
                $query->andWhere([
                    'and',
                    ['>=', 'delivery_date', $startDate.' 00:00:00'],
                    ['<=', 'delivery_date', $json['endDate'].' 23:59:59'],
                ]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['or',['like', 'user_name', "%$searchText%", false], ['like', 'user_phone', "%$searchText%", false]]);
            }
            $settlementStatus = isset($json['settlementStatus']) ? $json['settlementStatus'] : null;
            if ($settlementStatus != null) {
                $query->andWhere(['is_settlement' => $settlementStatus]);
            }
            $lineId = isset($json['lineId']) ? $json['lineId'] : null;
            if ($lineId != null) {
                $query->andWhere(['line_id' => $lineId]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum - 1, 'pageSize' => $pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        return [
            'total' => $count,
            'list' => $list
        ];
    }

    // 根据订单创建客户对账单
    public function createUserAuditByOrder($order, $orderDetail, $totalPrice) {
        // 保存对账单
        $userAudit = new UserAudit();
        $this->initAuditByOrder($userAudit, $order, $totalPrice);
        if (!$userAudit->save()) {
            throw new Exception($userAudit->errors);
        }
        // 保存对账单子表
        foreach ($orderDetail as $item) {
            $this->saveDetailByOrderDetail($item, $userAudit);
        }
    }

    private function saveDetailByOrderDetail($orderDetail, $userAudit) {
        $userAuditDetail = new UserAuditDetail();
        $userAuditDetail->user_audit_id = $userAudit->id;
        $userAuditDetail->user_audit_no = $userAudit->audit_no;
        $userAuditDetail->commodity_id = $orderDetail['commodity_id'];
        $userAuditDetail->commodity_name = $orderDetail['commodity_name'];
        $userAuditDetail->type_first_tier_id = $orderDetail['type_first_tier_id'];
        $userAuditDetail->type_id = $orderDetail['type_id'];
        $userAuditDetail->parent_type_name = $orderDetail['parent_type_name'];
        $userAuditDetail->type_name = $orderDetail['type_name'];
        $userAuditDetail->pic = $orderDetail['pic'];
        $userAuditDetail->unit = $orderDetail['unit'];
        $userAuditDetail->notice = $orderDetail['notice'];
        $userAuditDetail->in_price = $orderDetail['in_price'];
        $userAuditDetail->channel_type = $orderDetail['channel_type'];
        $userAuditDetail->price = $orderDetail['price'];
        $userAuditDetail->actual_num = $orderDetail['actual_num'];
        $userAuditDetail->total_price = $userAuditDetail->price * $userAuditDetail->actual_num;
        $userAuditDetail->diff_price = $userAuditDetail->price;
        $userAuditDetail->diff_num = $userAuditDetail->actual_num;
        $userAuditDetail->diff_total_price = $userAuditDetail->total_price;
        $userAuditDetail->is_basics_unit = $orderDetail['is_basics_unit'];
        $userAuditDetail->base_self_ratio = $orderDetail['base_self_ratio'];
        $userAuditDetail->base_unit = $orderDetail['base_unit'];
        if (!$userAuditDetail->save()) {
            throw new Exception('出库单明细保存失败');
        }
    }

    // 根据订单对账单初始化
    public function initAuditByOrder(&$userAudit, $order, $totalPrice) {
        $userAudit->audit_no = $this->getNo();
        $userAudit->source_id = $order['id'];
        $userAudit->source_no = $order['order_no'];
        $userAudit->user_id = $order['user_id'];
        $userAudit->user_name = $order['nick_name'];
        $userAudit->user_phone = $order['user_name'];
        $userAudit->operation_id = \Yii::$app->user->identity['id'];
        $userAudit->operation_name = \Yii::$app->user->identity['nickname'];
        $userAudit->line_id = $order['line_id'];
        $userAudit->line_name = $order['line_name'];
        $userAudit->line_sequence = $order['line_sequence'];
        $userAudit->driver_id = $order['driver_id'];
        $userAudit->driver_name = $order['driver_name'];
        $userAudit->delivery_date = $order['delivery_date'];
        $userAudit->delivery_time_detail = $order['delivery_time_detail'];
        $userAudit->total_price = $totalPrice;
        $userAudit->audit_price = $totalPrice;
    }

    /**
     * 生成对账单号
     * @return null|string
     */
    public function getNo()
    {
        $no = null;
        while (true) {
            $no = 'UA' . date('YmdHis') . mt_rand(10000, 99999);
            $exist_no = UserAudit::find()->where(['audit_no' => $no])->exists();
            if (!$exist_no) {
                break;
            }
        }
        return $no;
    }

    // 结算完成
    public function settlementSuccess($data) {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            // 修改对账单
            $userAuditId = $data['user_audit_id'];
            if ($userAuditId == null || $userAuditId == '') {
                throw new \yii\db\Exception('结算单号不存在');
            }
            $model3 = UserAudit::findOne($userAuditId);
            if ($model3 == null) {
                throw new \yii\db\Exception('结算单不存在');
            }
            $model3->is_settlement = 1;
            $model3->settlement_time = date('Y-m-d H:i:s', time());
            $settlementTotalPrice = $data['realPay'] + $data['reduction_price'];
            $model3->received_price = $model3->received_price + $settlementTotalPrice;
            if (!$model3->save()) {
                throw new Exception($model3->errors);
            }

            // 添加结算单
            $model = new FinanceAccountSettle();
            $model->attributes = $data;
            $model->settle_no = 'SE'.date('YmdHis').mt_rand(10000, 99999);
            $model->price = $settlementTotalPrice;
            $model->pay_way_text = $this->addPayWayText($data['pay_way']);
            $model->create_time = time();
            if (!$model->save()) {
                throw new Exception($model->errors);
            }
            // 添加结算单子表
            $model2 = new FinanceAccountSettleDetail();
            $model2->settle_id = $model->id;
            $model2->refer_no = $data['refer_no'];
            $model2->bill_type = $data['type'];
            $model2->bill_type_text = $this->addTypeText($data['type']);
            $model2->should_price = $data['audit_price'];
            $model2->pay_price = $data['received_price'];
            $model2->actual_price = $data['actual_price'];
            $model2->reduction_price = $data['reduction_price'];
            $model2->remark = $data['remarkDetail'];
            $model2->create_time = time();
            if (!$model2->save()) {
                throw new \yii\db\Exception($model2->errors);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 添加类型文字
    public static function addTypeText($type) {
        if ($type == 1) {
            return '销售订单';
        } else if ($type == 2) {
            return '多退少补';
        } else {
            return '其他';
        }
    }

    // 添加付款方式
    private function addPayWayText($payWay) {
        if ($payWay == 0) {
            return '微信';
        } else if ($payWay == 1) {
            return '支付宝';
        } else if ($payWay == 2) {
            return '转账';
        } else {
            return '现金';
        }
    }
}
