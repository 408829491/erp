<?php

namespace app\models\form;

use app\models\CommodityDemand;
use app\models\CommodityFavor;
use Yii;
use yii\data\Pagination;

class CommodityFavorForm extends \yii\base\Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $create_time;
    public $user;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['status',], 'default', 'value' => 0,],
            [['create_time',], 'default','value'=>''],
        ];
    }

    /**
     * 查询收藏列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return [];
        }
        $query = CommodityFavor::find();
        $query->where(['user_id'=> $this->user['id'],'bn_commodity_favor.status'=>1])
        ->select('bn_commodity.id,bn_commodity.name,bn_commodity.unit,bn_commodity.pic,bn_commodity.price')
        ->join('INNER JOIN','bn_commodity','bn_commodity_favor.commodity_id=bn_commodity.id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->asArray()
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as &$v){
            $v['pic'] = explode(':;',$v['pic'])[0];
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 收藏商品
     * @param $commodity_id
     * @return mixed
     */
    public function favor($commodity_id){
        $query = CommodityFavor::findOne(['commodity_id'=>$commodity_id,'user_id'=>$this->user['id']]);
        if(!$query){
            $model = new CommodityFavor();
            $data = [
                       'commodity_id' =>$commodity_id,
                       'status' =>1,
                       'user_id'=>$this->user['id'],
                       'create_time' =>time()
                   ];
            $model->attributes = $data;
            if($res = $model->save()){
                return 1;
            }else{
                return ['code'=>400,'msg'=>'收藏失败','data'=>[]];
            }
        }
        $query->status = ($query->status==0)?1:0;
        if($query->save()){
            return $query->status;
        }
        return ['code'=>400,'msg'=>'收藏失败','data'=>[]];
    }


    /**
     * 获取单个商品收藏状态
     * @param $id
     * @return mixed
     */
    public function getFavorStatus($id){
        $model = CommodityFavor::findOne(['commodity_id'=>$id,'user_id'=>$this->user['id']]);
        return isset($model->status)?$model->status:0;
    }

}
