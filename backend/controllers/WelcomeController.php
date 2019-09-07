<?php

namespace backend\controllers;

use app\models\form\WelcomeForm;

class WelcomeController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model= new WelcomeForm();
        $data = $model->getIndexStatistic();
        return $this->render('welcome',$data);
    }


}
