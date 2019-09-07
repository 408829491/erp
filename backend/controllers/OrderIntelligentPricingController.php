<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/6/5
 * Time: 15:53
 */

namespace backend\controllers;


use yii\web\Controller;

class OrderIntelligentPricingController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}