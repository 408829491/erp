<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\Customer;
use app\models\DeliveryDriver;
use app\models\DeliveryLine;
use app\models\Model;
use app\models\Order;
use app\models\OrderDetail;
use app\models\PrintConfig;
use app\models\PrintTemplate;
use app\models\Salesman;
use common\models\User;
use Yii;
use yii\data\Pagination;

class PrintForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $type;
    public $create_time;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['type',], 'default', 'value' => 'ORDER',],
            [['create_time',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        $this->type = $this->type ? $this->type : 'ORDER';
        $config = $this->getPrintConfig($this->type);
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = PrintTemplate::find();
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'name', $this->keyword],
            ]);
        }
        if ($this->type) {
            $query->andwhere(
                ['type'=>$this->type]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach ($list as &$v) {
            $v['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
            $v['config'] = $config;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 保存模板信息
     * @param int $id
     * @return bool
     */
    public function save()
    {
        $post = Yii::$app->request->post();
        $model = PrintTemplate::findOne($post['id']);
        $model->attributes = $post;
        $model->update_time = time();
        if ($model->save()) {
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }

    /**
     * 获取单个模板信息
     * @return mixed
     */
    public function getOne($id)
    {
        $model = PrintTemplate::findOne($id);
        $query = $model->toArray();
        return $query;
    }


    /**
     * 通过类型获取模板信息
     * @param $id
     * @return mixed
     */
    public function getTpl($type)
    {
        $model = PrintTemplate::find()
            ->where(['type'=>$type])
            ->asArray()
            ->one();
        return $model;
    }


    /**
     * 获取打印配置
     * @param string $type
     * @return array
     */
    public function getPrintConfig($type='ORDER'){
        $model = PrintConfig::findOne($type)->toArray();
        if($model){
            $model['PATH']['GET_FIELD']=Yii::$app->urlManager->createAbsoluteUrl(['plugins/cdsPrint/conf/print/editor/tpls']).'/'.$model['KEY'].'.html';
            $model['PATH']['GET_ONE']=Yii::$app->urlManager->createAbsoluteUrl(['print/get-one']);
            $model['PATH']['SAVE_DATA']=Yii::$app->urlManager->createAbsoluteUrl(['print/save-data']);
        }
        return $model;
    }

    /**
     * 获取单条分拣数据
     * @param $id
     * @return mixed
     */
    public function getPrintPickData($id){
        $model = OrderDetail::find();
        $model->select('nick_name,line_name,user_name,commodity_id,commodity_name
                                 ,num,actual_num,unit,bn_order.delivery_date,bn_order_detail.remark,sort_id,sort_name,sort_time,notice');
        $model->join('LEFT JOIN','bn_order','bn_order.id=bn_order_detail.order_id');
        if($ids = explode(',',$id)){
            $model->where(['in','bn_order_detail.id',$ids]);
        }else{
            $model->where(['bn_order_detail.id'=>$id]);
        }
        $query = $model->asArray()->all();
        return $query;
    }


    /**
     * 获取分拣汇总数据
     * @param $ids
     * @return mixed
     */
    public function getPrintPickDataAll($ids){
        $model = OrderDetail::find();
        $model->select('bn_order_detail.id as commodity_code,commodity_id,commodity_name,unit,sum(bn_order_detail.num) as total_amount,
                                 unit as unit_sell,user_id,bn_order.nick_name as user_name,bn_order_detail.remark as summary,line_name,bn_order.delivery_date,
                                 is_sorted as sort_status,bn_order_detail.stock_position as shelf_code');
        $model->join('LEFT JOIN','bn_order','bn_order.id=bn_order_detail.order_id');
        $model->groupBy('commodity_id');
        $model->where(['in', 'commodity_id', explode(',',$ids)]);
        $query = $model->asArray()->all();
        return ['create_time'=>date('Y-m-d H:i:s',time()),'user_name'=>$query[0]['user_name'],'detail'=>$query];
    }

}
