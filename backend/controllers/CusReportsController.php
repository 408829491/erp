<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/13
 * Time: 9:49
 */

namespace backend\controllers;


use app\models\form\CusReportsForm;
use backend\responses\ApiResponse;
use yii\web\Controller;

class CusReportsController extends Controller
{

    /**
     * 营业数据报表
     * @return string
     */
    public function actionTradeData()
    {
        return $this->render('tradeData');
    }


    /**
     * 营业数据报表数据
     * @return string
     */
    public function actionGetTradeDataList()
    {
        $model = new CusReportsForm();
        return new ApiResponse('200','ok',$model->getTradeData());
    }

    /**
     * 商品销量统计
     * @return string
     */
    public function actionGoodsSale()
    {
        return $this->render('goodsSale');
    }

    /**
     * 商品销量统计数据
     * @return string
     */
    public function actionGetGoodsSaleData()
    {
        $model = new CusReportsForm();
        return new ApiResponse('200','ok',$model->GetGoodsSaleData());
    }


    /**
     * 营业占比分析
     * @return string
     */
    public function actionSaleElement()
    {
        return $this->render('saleElement');
    }

    /**
     * 营业占比分析数据
     * @return string
     */
    public function actionGetSaleElementData()
    {
        $model = new CusReportsForm();
        return new ApiResponse('200','ok',$model->getSaleElementData());
    }

}