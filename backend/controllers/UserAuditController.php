<?php

namespace backend\controllers;

use app\models\DeliveryLine;
use app\models\FinanceAccountSettle;
use app\models\FinanceAccountSettleDetail;
use app\models\form\UserAuditForm;
use app\models\Order;
use app\models\UserAudit;
use app\models\UserAuditDetail;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\base\Exception;
use yii\helpers\Json;
use yii\web\Controller;

class UserAuditController extends Controller
{

    /**
     * 首页
     * @return string
     */
    public function actionIndex()
    {
        // 查询所有路线
        $lineData = DeliveryLine::find()->all();
        return $this->render('index', ['lineData' => $lineData]);
    }

    /**
     * 列表数据
     */
    public function actionIndexData($pageNum = 1, $pageSize = 10)
    {
        $userAudit = new UserAuditForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $userAudit->findPage($pageNum, $pageSize, \Yii::$app->request->get('filterProperty')));
    }

    // 查询关联订单是否完成
    public function actionFindOrderIsCompleted() {
        $id = \Yii::$app->request->get('id');
        if ($id == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $order = Order::findOne($id);

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $order->status == 2);
    }

    // 查看详情
    public function actionAuditDetailView() {
        $id = \Yii::$app->request->get('id');
        $userAudit = UserAudit::findOne($id);
        if ($userAudit == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $userAudit = $userAudit->toArray();
        $details = UserAuditDetail::find()->asArray()
            ->where(['user_audit_id' => $id])
            ->all();
        foreach ($details as &$item) {
            $item['subNum'] = $item['actual_num'] - $item['diff_num'];
            $item['subPrice'] = $item['price'] - $item['diff_price'];
            $item['subTotalPrice'] = $item['total_price'] - $item['diff_total_price'];
        }
        $userAudit['details'] = $details;

        return $this->render('auditDetailView', $userAudit);
    }

    // 对账编辑
    public function actionAuditUpdate() {
        $id = \Yii::$app->request->get('id');
        $userAudit = UserAudit::findOne($id);
        if ($userAudit == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $userAudit = $userAudit->toArray();
        $details = UserAuditDetail::find()->asArray()
            ->where(['user_audit_id' => $id])
            ->all();
        foreach ($details as &$item) {
            $item['subNum'] = $item['actual_num'] - $item['diff_num'];
            $item['subPrice'] = $item['price'] - $item['diff_price'];
            $item['subTotalPrice'] = $item['total_price'] - $item['diff_total_price'];
        }
        $userAudit['details'] = $details;

        return $this->render('auditUpdate', $userAudit);
    }

    // 修改保存
    public function actionUpdateSave() {
        $id = \Yii::$app->request->post('id');
        if ($id == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $userAudit = UserAudit::findOne($id);
        if ($userAudit == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }

        $userAudit->attributes = \Yii::$app->request->post();
        $userAudit->audit_price = \Yii::$app->request->post('auditPrice');
        $userAudit->is_audit = 1;
        $userAudit->audit_man_id = \Yii::$app->user->identity['id'];
        $userAudit->audit_man_name = \Yii::$app->user->identity['nickname'];
        $userAudit->audit_time = date('Y-m-d H:i:s', time());
        if (!$userAudit->save()) {
            throw new Exception('对账单修改失败');
        }

        // 修改子表数据
        $this->updateDetailSave(\Yii::$app->request->post('details'));

        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $userAudit->toArray());
    }

    // 修改子表数据
    private function updateDetailSave($details) {
        $detailsObj = Json::decode($details);
        foreach ($detailsObj as $item) {
            $detail = UserAuditDetail::findOne($item['id']);
            $detail->attributes = $item;
            if (!$detail->save()) {
                throw new Exception('对账单子表修改失败');
            }
        }
    }

    // 结算编辑
    public function actionSettlementUpdate() {
        $id = \Yii::$app->request->get('id');
        $userAudit = UserAudit::findOne($id);
        if ($userAudit == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, 'fail');
        }
        $userAudit = $userAudit->toArray();

        return $this->render('settlementUpdate', ['model' => $userAudit]);
    }

    // 结算保存
    public function actionSettlementSave() {
        $data = \Yii::$app->request->post();
        $userAudit = new UserAuditForm();
        $userAudit->settlementSuccess($data);
        return new ApiResponse();
    }

    // 查询结账记录
    public function actionAuditedList() {
        $orderId = \Yii::$app->request->get('orderId');

        $list = FinanceAccountSettleDetail::find()->asArray()
            ->select('bn_finance_account_settle_detail.*, bn_finance_account_settle.settle_no, bn_finance_account_settle.create_user, bn_finance_account_settle.pay_user, bn_finance_account_settle.pay_way')
            ->leftJoin('bn_finance_account_settle', 'bn_finance_account_settle.id = bn_finance_account_settle_detail.settle_id')
            ->where(['bn_finance_account_settle.refer_no' => $orderId])
            ->all();

        // 切换日期
        foreach ($list as &$item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        return $this->render('auditedList', ['list' => $list]);
    }
}