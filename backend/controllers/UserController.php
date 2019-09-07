<?php

namespace backend\controllers;

use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use common\components\nickname;
use common\models\User;
use Yii;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends Controller
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
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Customer::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetUserList(){
        $model = User::find();
        $keyword = Yii::$app->request->get('keyword');
        if($keyword!=='a'){
            $model->filterWhere(['like','nickname',$keyword]);
        }
        $data = $model->asArray()
              ->select('id,username,nickname,address,contact_name,line_id')->all();

        return json_encode(['code'=>0,'msg'=>'ok','data'=>['total'=>20,'list'=>$data]]);
    }

    /**
     * Displays a single Customer model.
     * @param integer $id
     * @param string $pay_way
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $pay_way)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $pay_way),
        ]);
    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'pay_way' => $model->pay_way]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $pay_way
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $pay_way)
    {
        $model = $this->findModel($id, $pay_way);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'pay_way' => $model->pay_way]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $pay_way
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $pay_way)
    {
        $this->findModel($id, $pay_way)->delete();

        return $this->redirect(['index']);
    }

    public function actionCheckUsernameIsUsed() {
        
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $pay_way
     * @return CustomerController the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $pay_way)
    {
        if (($model = Customer::findOne(['id' => $id, 'pay_way' => $pay_way])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
