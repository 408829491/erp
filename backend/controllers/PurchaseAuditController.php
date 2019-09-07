<?php

namespace backend\controllers;

use app\models\form\PurchaseAuditForm;
use app\models\Purchase;
use app\models\PurchaseAuditDetail;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


class PurchaseAuditController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }


    /**
     * 获取采购对账单列表数据
     */
    public function actionPurchaseList()
    {
        $model = new PurchaseAuditForm();
        $data = $model->search();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * 获取采购对账详情数据
     */

    public function actionPurchaseAuditDetail($id)
    {
        $query = PurchaseAuditDetail::find();
        $data['list'] = $query->asArray()
            ->where(['purchase_id' => $id])
            ->orderBy('id')
            ->all();
        $data['total'] = $query->count();
        return (new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data));
    }


    /**
     * Finds the Purchase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $source
     * @param string $source_txt
     * @param integer $create_time
     * @return Purchase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Purchase::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
