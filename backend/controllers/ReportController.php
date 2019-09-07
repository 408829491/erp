<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/13
 * Time: 9:49
 */

namespace backend\controllers;


use yii\web\Controller;

class ReportController extends Controller
{
    public function actionUserPanel()
    {
        return $this->render('userPanel');
    }
}