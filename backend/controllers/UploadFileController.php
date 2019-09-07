<?php

namespace backend\controllers;

use app\models\UploadFile;
use backend\responses\ApiResponse;
use Yii;
use yii\web\UploadedFile;

class UploadFileController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = new UploadFile(); // 实例化上传类
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstanceByName('file'); //使用UploadedFile的getInstance方法接收单个文件
            $model->setScenario('upload'); // 设置upload场景
            $res = $model->uploadFile(); //调用model里边的upload方法执行上传
            //$err = $model->getErrors(); //获取错误信息
            return new ApiResponse('200','success',$res);
        }

        return $this->render('index',['model'=>$model]);
    }

    public function test()
    {
        $model = new UploadFile(); // 实例化上传类
        return $this->render('index',['model'=>$model]);
    }

}
