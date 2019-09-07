<?php

namespace backend\controllers;

use app\models\CusMember;
use app\models\form\CusMemberForm;
use app\models\form\CustomerForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;

class CusMemberController extends Controller
{
    /**
     * 会员列表
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 创建页面
     */
    public function actionCreate()
    {
        return $this->render('create');
    }


    /**
     * 列表数据
     */
    public function actionGetIndexData()
    {
        $model = new CusMemberForm();
        return new ApiResponse(200, 'ok', $model->search());
    }

    /**
     * 保存会员
     */
    public function actionSave($id = 0)
    {
        $model = new CusMemberForm();
        $res = $model->save($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

}
