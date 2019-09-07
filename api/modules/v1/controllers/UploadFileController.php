<?php

namespace api\modules\v1\controllers;

use app\models\UploadFiles;
use Yii;
use yii\web\UploadedFile;

class UploadFileController extends Controller
{
    public function actionUpload()
    {
        $model = new UploadFiles();//实例化上传类
        if (Yii::$app->request->isPost) {
            $model->files = UploadedFile::getInstanceByName('file'); //使用UploadedFile的getInstance方法接收单个文件
            $model->setScenario('upload'); // 设置upload场景
            $res = $model->uploadFile(); //调用model里边的upload方法执行上传
            //$err = $model->getErrors(); //获取错误信息
            return ['code'=>200,'msg'=>'上传成功','data'=>$res];
        }
        return ['code'=>400,'msg'=>'上传失败','data'=>[]];
    }

}
