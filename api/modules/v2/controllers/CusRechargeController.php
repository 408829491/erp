<?php

namespace api\modules\v2\controllers;

use app\models\CusRechargeRule;

/**
 * C端商品
 */
class CusRechargeController extends Controller
{
    // 查询充值金额赠送规则
    public function actionFindAll() {
        $dataList = CusRechargeRule::find()
            ->all();

        return $dataList;
    }
}