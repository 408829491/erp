<?php

namespace backend\controllers;
use yii\web\Controller;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderChangePriceController extends Controller
{
    public function actionNew()
    {
        return $this->render('new');
    }

}