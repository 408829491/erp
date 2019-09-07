<?php

namespace backend\controllers;

use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use app\models\CommodityCategory;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommodityCategoryController implements the CRUD actions for CommodityCategory model.
 */
class CommodityCategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CommodityCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData()
    {
        $query = CommodityCategory::find();
        $data = $query->select(['id', 'name', 'sequence', 'pid'])->asArray()->all();

        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok", $data);
    }

    // 访问第一层分类
    public function actionFirstTierData() {
        $query = new CommodityCategory();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findFirstTierData(['id','name','pid']));
    }

    /**
     * Displays a single CommodityCategory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CommodityCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pid)
    {
        return $this->render('create',['pid'=>$pid]);
    }

    public function actionSave(){
        $sequence = Yii::$app->request->post('sequence',0);
        $saveData = new CommodityCategory();
        $saveData->name = Yii::$app->request->post('name');
        $saveData->sequence = $sequence == null ? 0 : $sequence;
        $saveData->pid = Yii::$app->request->post('pid');
        $saveData->save();

        return new ApiResponse();
    }

    /**
     * Updates an existing CommodityCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionEdit() {
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $sequence = Yii::$app->request->post('sequence');

        CommodityCategory::updateAll(['name' => $name,'sequence' => $sequence],"id = $id");

        return new ApiResponse();
    }

    /**
     * Deletes an existing CommodityCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids == null) {
            return new ApiResponse(ApiCode::CODE_ERROR,"失败");
        }
        $ids2 = explode(",",$ids);
        foreach ( $ids2 as $id) {
            CommodityCategory::deleteAll(['id' => $id]);
            CommodityCategory::deleteAll(['pid' => $id]);
        }
        return new ApiResponse();
    }

    /**
     * Finds the CommodityCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommodityCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CommodityCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
