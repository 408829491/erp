<?php

namespace backend\controllers;

use app\models\CommodityProfile;
use app\models\CommodityProfileDetail;
use app\models\Config;
use app\models\CustomerType;
use app\models\form\CommodityForm;
use app\models\form\SetPriceForm;
use backend\responses\ApiCode;
use backend\responses\ApiResponse;
use Yii;
use app\models\Commodity;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommodityListController implements the CRUD actions for Commodity model.
 */
class CommodityListController extends Controller
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
     * 首页
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIndexData($pageNum=0,$pageSize=10) {
        $query = new Commodity();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$query->findPage($pageNum,$pageSize,Yii::$app->request->get('filterProperty'),null));
    }

    /**
     * 商品选择
     */
    public function actionList()
    {
        return $this->render('list',[]);
    }


    /**
     * 获取商品列表数据
     * @return ApiResponse
     */
    public function actionGetListData(){
        $model = new CommodityForm();
        return new ApiResponse(ApiCode::CODE_SUCCESS,"ok",$model->getCommodityData());
    }



    /**
     * Lists all Commodity models.
     * @return mixed
     */
    public function actionBaseCommodity()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Commodity::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Commodity model.
     * @param integer $id
     * @param string $name
     * @param integer $type_id
     * @param string $price
     * @param string $in_price
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $name, $type_id, $price, $in_price)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $name, $type_id, $price, $in_price),
        ]);
    }

    /**
     * Creates a new Commodity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create');
    }


    /**
     * 设置客户类型价
     * @return string
     */
    public function actionSetPrice($id = 0,$unit='')
    {
        $model = new SetPriceForm();
        $data['setting'] = $model->getPriceSetting($id,$unit);
        return $this->render('setPrice',$data);
    }

    /**
     * 设置客户类型价
     * @return string
     */
    public function actionBatchSetPrice()
    {
        return $this->render('batchSetPrice');
    }

    /**
     * 保存价格设置信息
     * @return ApiResponse
     */
    public function actionSetPriceSave(){
        $model = new SetPriceForm();
        $model->settingSave();
        return new ApiResponse();
    }

    // 保存
    public function actionSave(){

        $model = new Commodity();

        $model->attributes = Yii::$app->request->post();

        $model->is_online = Yii::$app->request->post('is_online') != null ? 1 : 0;
        $model->is_time_price = Yii::$app->request->post('is_time_price') != null ? 1 : 0;
        $model->channel_type = explode(',',Yii::$app->request->post('purchase_ids'))[0];
        $model->agent_id = explode(',',Yii::$app->request->post('purchase_ids'))[1];
        if (!$model->validate()) {
            new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors);
        };
        $model->saveData($model, Yii::$app->request->post('unitList'));

        return new ApiResponse();
    }

    /**
     * Updates an existing Commodity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $name
     * @param integer $type_id
     * @param string $price
     * @param string $in_price
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $modelSubBasicUnit = CommodityProfile::find()->asArray()->where(['is_basics_unit'=>1,'commodity_id'=>$id])->one();
        $unitModel = Config::findOne("25")->toArray();
        $modelSubUnitLList = CommodityProfile::find()->asArray()->where(['is_basics_unit'=>0,'commodity_id'=>$id])->all();
        // 获取所有价格类型
        $customerTypeData = CustomerType::find()->asArray()->all();
        $this->findPriceList($customerTypeData, $modelSubBasicUnit);

        foreach ($modelSubUnitLList as &$item) {
            $this->findPriceList($customerTypeData, $item);
        }
        return $this->render('update',['id'=>$id, 'customerTypeData' => $customerTypeData, 'model'=>Commodity::findOne($id)->toArray(), 'modelSubBasicUnit'=>$modelSubBasicUnit, 'modelSubUnitLList'=>$modelSubUnitLList, 'unitList'=>explode(":;", $unitModel['value'])]);
    }

    // 查询价格list
    private function findPriceList($customerTypeData, &$data) {
        $list = [];
        foreach ($customerTypeData as $item) {
            // 查询是否已经添加了所属单位的价格
            $data2 = CommodityProfileDetail::findOne(['commodity_profile_id' => $data['id'], 'type_id' => $item['id']]);
            if ($data2 != null) {
                $item['price'] = $data2->price;
            } else {
                $item['price'] = 0;
            }
            array_push($list, $item);
        }

        $data['priceList'] = $list;
    }

    // 修改保存
    public function actionEdit() {

        $model = Commodity::findOne(Yii::$app->request->post('id'));
        $model->attributes = Yii::$app->request->post();

        $model->is_online = Yii::$app->request->post('is_online') != null ? 1 : 0;
        $model->is_time_price = Yii::$app->request->post('is_time_price') != null ? 1 : 0;

        $model->channel_type = explode(',',Yii::$app->request->post('purchase_ids'))[0];
        $model->agent_id = explode(',',Yii::$app->request->post('purchase_ids'))[1];

        if (!$model->validate()) {
            return new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors);
        }
        $model->edit($model, Yii::$app->request->post('unitList'));

        return new ApiResponse();
    }

    // 修改上下架
    public function actionUpdateIsOnline() {
        $is_online = Yii::$app->request->post('is_online') == 'true' ? 1 : 0;
        Commodity::updateAll(['is_online'=>$is_online] , ['id'=>Yii::$app->request->post('id')]);
        return new ApiResponse();
    }

    /**
     * Deletes an existing Commodity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $name
     * @param integer $type_id
     * @param string $price
     * @param string $in_price
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
            Commodity::deleteAll(['id' => $id]);
        }
        return new ApiResponse();
    }


    /**
     * 更新商品进货价
     * @return ApiResponse
     */
    public function actionChangeInPrice($id,$price){
        $model = new Commodity();
        if($model->changeInPrice($id,$price)){
            return new ApiResponse();
        }
        return new ApiResponse(ApiCode::CODE_ERROR, '失败', $model->errors);
    }


    /**
     * 智能订价历史记录
     * @return ApiResponse
     */
    public function actionSettingPriceHistory(){
        $model = new CommodityForm();
        return new ApiResponse('200','ok',$model->getSettingPriceHistory());
    }

    /**
     * Finds the Commodity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $name
     * @param integer $type_id
     * @param string $price
     * @param string $in_price
     * @return Commodity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $name, $type_id, $price, $in_price)
    {
        if (($model = Commodity::findOne(['id' => $id, 'name' => $name, 'type_id' => $type_id, 'price' => $price, 'in_price' => $in_price])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
