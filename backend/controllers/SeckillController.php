<?php

namespace backend\controllers;

use app\models\SeckillCommodity;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use app\models\Seckill;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SeckillController implements the CRUD actions for Seckill model.
 */
class SeckillController extends Controller
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
     * Lists all Seckill models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum,$pageSize) {
        $query = new Seckill();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findPage($pageNum,$pageSize,Yii::$app->request->get('filterProperty'),null));
    }

    /**
     * Displays a single Seckill model.
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
     * Creates a new Seckill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionSave() {
        $model = new Seckill();
        $model->attributes = Yii::$app->request->post();
        $model->saveData($model, Yii::$app->request->post('subList'));

        return new ApiResponse();
    }

    /**
     * Updates an existing Seckill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $subList = SeckillCommodity::find()->where(['seckill_id' => $id])->select('commodity_id as id,is_online,name,pic,unit,price,alias,channel_type,notice,type_id,type_first_tier_id,activity_price,limit_buy')->asArray()->all();
        return $this->render('update', [
            'model' => Seckill::findOne($id),
            'subList' => json_encode($subList)
        ]);
    }

    public function actionEdit() {
        $model = Seckill::findOne(Yii::$app->request->post('id'));
        $model->attributes = Yii::$app->request->post();
        $model->editData($model, Yii::$app->request->post('subList'), Yii::$app->request->post('id'));

        return new ApiResponse();
    }

    /**
     * Deletes an existing Seckill model.
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
        Seckill::deleteAll(['id'=>$ids]);
        return new ApiResponse();
    }


    // 关闭活动
    public function actionClose() {
        $model = Seckill::findOne(Yii::$app->request->post('id'));
        if ($model == null) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败');
        }
        // 判断活动是否结束
        if ($model->is_close == 1 || strtotime($model->end_time) < strtotime('now')) {
            return new ApiResponse(ApiCode::CODE_SUCCESS, 'Ok', ['code' => '0']);
        }
        $model->is_close = 1;
        $model->close_time = date('Y-m-d H:i:s');
        $model->closer_id = Yii::$app->user->identity->id;
        $model->close_name = Yii::$app->user->identity->nickname;

        $model->save();
        return new ApiResponse(ApiCode::CODE_SUCCESS, 'Ok', ['code' => '1']);
    }

    /**
     * Finds the Seckill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Seckill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Seckill::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
