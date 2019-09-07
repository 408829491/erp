<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/4/10
 * Time: 18:39
 */

namespace backend\controllers;


use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex(){
        $data=[
            'name'=>'Mr Li',
            'general'=>'women'
        ];

        return new ApiResponse(ApiCode::CODE_SUCCESS, '成功', $data);
    }

}