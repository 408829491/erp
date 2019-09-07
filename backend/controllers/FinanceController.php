<?php

namespace backend\controllers;

use app\models\form\FinanceForm;
use app\models\form\OrderForm;
use app\models\form\PurchaseAuditForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use common\models\User;
use yii\web\Controller;


class FinanceController extends Controller
{


    /**
     * 客户结算列表
     */
    public function actionUserAuditList()
    {
        return $this->render('userAuditList');
    }


    /**
     * 采购结算单
     */
    public function actionPurchaseSettlementList()
    {
        return $this->render('purchaseSettlementList');
    }

    /**
     * 客户对账
     */
    public function actionAudit($id)
    {
        $model = new OrderForm();
        return $this->render('audit', $model->getData($id));
    }

    /**
     * 采购对账
     */
    public function actionPurchaseAudit($id)
    {
        $model = new PurchaseAuditForm();
        return $this->render('purchaseAudit', $model->getData($id));
    }

    /**
     * 采购对账单详情
     */
    public function actionPurchaseAuditDetail($id)
    {
        $model = new PurchaseAuditForm();
        return $this->render('purchaseAuditDetail',$model->getData($id));
    }

    /**
     * 采购对账单详情
     */
    public function actionPurchaseAuditSave()
    {
        $model = new PurchaseAuditForm();
        $res = $model->save();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }

    /**
     * 客户结算
     */
    public function actionSettlement($id)
    {
        $model = new OrderForm();
        $data = $model->getData($id);
        return $this->render('settlement', ['model' => $data, 'list' => json_encode([$data], true)]);
    }

    /**
     * 采购结算
     */
    public function actionPurchaseSettlement($id)
    {
        $model = new PurchaseAuditForm();
        $data = $model->getData($id);
        return $this->render('purchaseSettlement', ['model' => $data, 'list' => json_encode([$data], true)]);
    }

    /**
     * 获取对账单列表数据
     */
    public function actionGetUserAuditList()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->search());
    }

    /**
     * 获取结算单列表数据
     */
    public function actionGetSettleList()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->settleList());
    }

    /**
     * 获取采购结算单列表数据
     */
    public function actionGetPurchaseSettleList()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->settlePurchaseList());
    }

    /**
     * 客户结算单
     */
    public function actionUserSettlementList()
    {
        return $this->render('userSettlementList');
    }

    /**
     * 客户结算单详情
     */
    public function actionSettlementDetail($id)
    {
        $model = new FinanceForm();
        $data = $model->getSettleDetail($id);
        return $this->render('settlementDetail', $model->getSettleDetail($id));
    }

    /**
     * 采购结算单详情
     */
    public function actionPurchaseSettlementDetail($id)
    {
        $model = new FinanceForm();
        return $this->render('purchaseSettlementDetail', $model->getSettlePurchaseDetail($id));
    }

    /**
     * 采购结算
     */
    public function actionPurchaseList()
    {
        $model = new PurchaseAuditForm();
        return new ApiResponse('200', 'ok', $model->search());
    }

    /**
     * 客户余额
     */
    public function actionUserBalanceList()
    {
        return $this->render('userBalanceList');
    }

    /**
     * 采购结算单
     */
    public function actionPurchaseAuditList()
    {
        return $this->render('purchaseAuditList');
    }


    /**
     * 客户账款
     */
    public function actionReceiptSum()
    {
        return $this->render('receiptSum');
    }

    /**
     * 获取客户账款数据
     */
    public function actionGetReceiptSumData(){
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->GetReceiptSum());
    }


    /**
     * 创建结算单
     * @return mixed
     */
    public function actionSave()
    {
        $model = new FinanceForm();
        $res = $model->save();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }


    /**
     * 创建采购结算单
     * @return mixed
     */
    public function actionSavePurchase()
    {
        $model = new FinanceForm();
        $res = $model->savePurchase();
        if (true === $res) {
            return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', []));
        }
        return (new ApiResponse(ApiCode::CODE_ERROR, '失败', $res['data']));
    }


    /**
     * 结算详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = new OrderForm();
        return $this->render('view', $model->getData($id));
    }

    /**
     * 获取客户列表数据
     * @return mixed
     */
    public function actionBalance()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->getUserBalance());
    }


    /**
     * 充值记录列表
     * @param $id
     * @return mixed
     */
    public function actionRechargeRecord($id)
    {
        $model = User::findOne($id)->toArray();
        return $this->render('rechargeRecord', $model);
    }

    /**
     * 获取充值记录数据
     * @return ApiResponse
     */
    public function actionGetRechargeData($id)
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->getRechargeData($id));
    }

    /**
     * 收支记录列表
     * @param $id
     * @return mixed
     */
    public function actionBalanceRecord($id)
    {
        $model = User::findOne($id)->toArray();
        return $this->render('balanceRecord', $model);
    }

    /**
     * 获取收支列表数据
     * @return ApiResponse
     */
    public function actionGetBalanceData($id = null)
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->getBalanceData($id));
    }


    /**
     * 充值
     * @param $id
     * @return mixed
     */
    public function actionRecharge($id)
    {
        $model = User::findOne($id)->toArray();
        return $this->render('recharge', $model);
    }

    /**
     * 扣款
     * @param $id
     * @return mixed
     */
    public function actionWithhold($id)
    {
        $model = User::findOne($id)->toArray();
        return $this->render('withhold', $model);
    }


    /**
     * 提交扣款
     * @return mixed
     */
    public function actionConfirmWithhold()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->saveBalance());
    }

    /**
     * 提交充值
     * @return mixed
     */
    public function actionConfirmRecharge()
    {
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->saveBalance());
    }


    /**
     * 结算记录列表
     * @param $id
     * @return mixed
     */
    public function actionPurchaseSettlementRecord()
    {
        return $this->render('purchaseSettlementRecord');
    }

    /**
     * 获取结算记录数据
     * @param $refer_no
     * @return ApiResponse
     */
    public function actionGetPurchaseSettlementRecord($refer_no){
        $model = new FinanceForm();
        return new ApiResponse('200', 'ok', $model->purchaseSettlementRecord($refer_no));
    }
}
