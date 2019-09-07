<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/13
 * Time: 9:49
 */

namespace backend\controllers;


use app\models\form\ReportsForm;
use backend\responses\ApiResponse;
use yii\web\Controller;

class ReportsController extends Controller
{


    /**
     * 毛利统计
     * @return string
     */
    public function actionProfitStatistics()
    {
        return $this->render('userPanel');
    }


    /**
     * 订单统计
     * @return string
     */
    public function actionOrderState()
    {
        return $this->render('userPanel');
    }


    /**
     * 客户统计
     * @return string
     */
    public function actionUserStatistics()
    {
        return $this->render('userStatistics');
    }


    /**
     * 业务员销售统计
     * @return string
     */
    public function actionSalesStatistics()
    {
        return $this->render('salesStatistics');
    }


    /**
     * 采购统计
     * @return string
     */
    public function actionPurchase()
    {
        $model = new ReportsForm();
        return $this->render('purchase', $model->getPurchaseSummary());
    }


    /**
     * 财务流水表
     * @return string
     */
    public function actionReportSuper()
    {
        return $this->render('userPanel');
    }

    /**
     * 进销存报表
     * @return string
     */
    public function actionInventoryInvoicing()
    {
        return $this->render('inventoryInvoicing');
    }


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
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getTradeData());
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
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getGoodsSaleData());
    }

    /**
     * 获取用户统计数据
     * @return ApiResponse
     */
    public function actionGetUserStatistics()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getUserStatistics());
    }

    /**
     * 获取用户统计数据
     * @return ApiResponse
     */
    public function actionGetSalesStatistics()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getSalesStatistics());
    }

    /**
     * 获取采购统计数据
     * @return ApiResponse
     */
    public function actionGetPurchaseStatistics()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getPurchaseStatistics());
    }

    /* 获取采购统计数据按商品
     * @return ApiResponse
     */
    public function actionGetPurchaseStatisticsByCommodity()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getPurchaseStatisticsByCommodity());
    }

    /* 获取采购统计数据按供应商
     * @return ApiResponse
     */
    public function actionGetPurchaseStatisticsByProvider()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getPurchaseStatisticsByProvider());
    }

    /* 获取采购统计数据按采购员
 * @return ApiResponse
 */
    public function actionGetPurchaseStatisticsByBuyer()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getPurchaseStatisticsByBuyer());
    }

    /* 获取采购统计数据按采购员
     * @return ApiResponse
     */
    public function actionGetInventoryInvoicingStatistics()
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getInventoryInvoicingStatistics());
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
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getSaleElementData());
    }

    /**
     * 价格波动表
     * @return string
     */
    public function actionPriceFluctuation()
    {
        return $this->render('priceFluctuation');
    }

    /**
     * 获取价格波动数据
     * @param $commodity_id
     * @return ApiResponse
     */
    public function actionGetPriceFluctuation($commodity_id = 0,$unit = '')
    {
        $model = new ReportsForm();
        return new ApiResponse('200', 'ok', $model->getCommodityPriceFluctuation($commodity_id,$unit));
    }

}