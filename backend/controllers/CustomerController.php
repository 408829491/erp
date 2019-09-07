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
use app\models\Salesman;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CustomerController extends Controller
{


    /**
     * 客户列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'c_type' => CustomerType::find()->all(),
            'area' => $area = Area::find()->all()//区域
        ]);
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
        $model = new CustomerForm();
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
        $model = new CustomerForm();
        return new ApiResponse(200, 'ok', $model->search());
    }


    /**
     * 开启关闭客户状态
     */
    public function actionChangeStatus($id)
    {
        $model = new CustomerForm();
        $res = $model->changeStatus($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }

    /**
     * 客户编辑
     * @param $id
     * @return string
     */
    public function actionUpdate($id)
    {
        $model = Customer::findOne(['id' => $id]);
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
        $model = Customer::findOne(['id' => $id]);
        $formInfo = $this->getFormBaseInfo();
        return $this->render('view', [
            'model' => $model,
            'formInfo' => $formInfo
        ]);
    }

    /**
     * 客户删除
     * @param $id
     * @return string
     */
    public function actionDel($id)
    {
        $model = new CustomerForm();
        $res = $model->deleteUser($id);
        if (true === $res) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'ok', []);
        }
        return new ApiResponse(ApiCode::CODE_ERROR, 'false', $res);
    }


    /**
     * 初始化模型
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 获取表单基本信息
     * @return array
     */
    protected function getFormBaseInfo()
    {
        $c_type = CustomerType::find()->all();//客户类型
        $sale_man = Salesman::find()->all();//销售员
        $area = Area::find()->all();//区域
        $line = DeliveryLine::find()->all();//线路
        $delivery_time = DeliveryTime::find()->all();//配送时间
        return [
            'c_type' => $c_type,
            'sale_man' => $sale_man,
            'area' => $area,
            'line' => $line,
            'delivery_time' => $delivery_time,
        ];
    }

}