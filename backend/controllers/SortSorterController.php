<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/5/11
 * Time: 9:30
 */

namespace backend\controllers;


use app\models\Area;
use app\models\Customer;
use app\models\CustomerType;
use app\models\DeliveryLine;
use app\models\DeliveryTime;
use app\models\form\CustomerForm;
use app\models\form\SortSorterForm;
use app\models\Salesman;
use app\models\SortSorter;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SortSorterController extends Controller
{


    /**
     * 客户列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 新增客户
     * @return mixed
     */
    public function actionCreate()
    {
        $formInfo = $this->getFormBaseInfo();
        return $this->render('create', ['formInfo' => $formInfo]);
    }

    /**
     * 保存客户信息
     * @return ApiResponse
     */
    public function actionSave($id = 0)
    {
        $model = new SortSorterForm();
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
        $model = new SortSorterForm();
        return new ApiResponse(200, 'ok', $model->search());
    }



    /**
     * 客户编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = SortSorter::findOne(['id' => $id]);
        $formInfo = $this->getFormBaseInfo();
        return $this->render('update', [
            'model' => $model,
            'formInfo' => $formInfo
        ]);
    }


    /**
     * 客户详情
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $model = SortSorter::findOne(['id' => $id]);
        $formInfo = $this->getFormBaseInfo();
        return $this->render('view', [
            'model' => $model,
            'formInfo' => $formInfo
        ]);
    }

    /**
     * 客户编辑
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new SortSorterForm();
        $res = $model->deleteUser($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }


    public function getFormBaseInfo(){
        return;
    }


    public function actionGetCateList($id = 0){
        $model = new SortSorterForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', $model->getCommodityCate($id));
    }
    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = SortSorter::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}