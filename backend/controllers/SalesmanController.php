<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;

use app\models\form\SalesmanForm;
use app\models\Salesman;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SalesmanController extends Controller
{


    /**
     * 销售员列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 新增销售员
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    /**
     * 保存销售员信息
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new SalesmanForm();
        $res = $model->save($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 列表数据
     */
    public function actionGetIndexData()
    {
        $model = new SalesmanForm();
        return new ApiResponse(200, 'ok', $model->search());
    }


    /**
     * 开启关闭客户状态
     */
    public function actionChangeStatus($id)
    {
        $model = new SalesmanForm();
        $res = $model->changeStatus($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 销售员编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = Salesman::findOne(['id' => $id]);
        return $this->render('update', [
            'model' => $model
        ]);
    }


    /**
     * 销售员详情
     * @param $id
     * @return string
     */
    public function actionView($code)
    {
        return $this->render('view', [
            'code' => $code
        ]);
    }

    /**
     * 删除销售员
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new SalesmanForm();
        $res = $model->deleteUser($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 获取推广客户列表
     * @param $code
     * @return mixed
     */
    public function actionGetCustomerList($code)
    {
        $model = new SalesmanForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->getCustomerList($code));
    }


    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Salesman::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}