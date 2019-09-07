<?php

namespace app\models\form;

use app\models\CommodityDemand;
use app\models\CommodityFavor;
use app\models\CusCommodityFavor;
use app\models\CusCommodityProfile;
use Yii;
use yii\data\Pagination;

class CusCommodityFavorForm extends \yii\base\Model
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

    public function searchByFirstTierTypeId($storeId, $bigTypeId)
    {
        $query = CusCommodityFavor::find()->asArray()
            ->select('bn_cus_commodity.id, bn_cus_commodity.`name`, bn_cus_commodity.unit, bn_cus_commodity.pic, bn_cus_commodity.price, bn_cus_commodity.pic, bn_cus_commodity.summary')
            ->join('INNER JOIN','bn_cus_commodity','bn_cus_commodity_favor.commodity_id = bn_cus_commodity.id')
            ->where(['bn_cus_commodity_favor.user_id' => $this->user['id'],'bn_cus_commodity_favor.status' => 1, 'bn_cus_commodity_favor.store_id' => $storeId]);

        if ($bigTypeId != null && $bigTypeId != -1) {
            $query->andWhere(['bn_cus_commodity.type_first_tier_id' => $bigTypeId]);
        }

        $list = $query
            ->orderBy('id DESC')
            ->all();

        foreach ($list as &$v) {
            if (empty($v['summary'])) {
                $v['summary'] = '时价好物，实惠亲民';
            }
            // 只取第一张图片
            $v['pic']= explode(':;', $v['pic'])[0];
            // 查询可售卖的单位
            $cusCommodityProfile = CusCommodityProfile::find()->asArray()
                ->where(['commodity_id' => $v['id'], 'is_sell' => 1])
                ->one();
            $v['basic_unit'] = $v['unit'];
            $v['unit'] = $cusCommodityProfile['name'];
            $v['is_basics_unit'] = $cusCommodityProfile['is_basics_unit'];
            $v['price'] = $cusCommodityProfile['price'];
            $v['base_self_ratio'] = $cusCommodityProfile['base_self_ratio'];
        }

        return $list;
    }

    /**
     * 查询收藏列表
     * @return array
     */
    public function search($storeId)
    {
        // 查询大分类
        $query1 = CusCommodityFavor::find()->asArray()
            ->select('bn_cus_commodity_category.id, bn_cus_commodity_category.`name`')
            ->leftJoin('bn_cus_commodity','bn_cus_commodity_favor.commodity_id = bn_cus_commodity.id')
            ->leftJoin('bn_cus_commodity_category', 'bn_cus_commodity.type_first_tier_id = bn_cus_commodity_category.id')
            ->where(['bn_cus_commodity_favor.user_id' => $this->user['id'],'bn_cus_commodity_favor.status' => 1, 'bn_cus_commodity_favor.store_id' => $storeId])
            ->groupBy('bn_cus_commodity.type_first_tier_id');

        $query = CusCommodityFavor::find()->asArray()
            ->select('bn_cus_commodity.id, bn_cus_commodity.`name`, bn_cus_commodity.unit, bn_cus_commodity.pic, bn_cus_commodity.price, bn_cus_commodity.pic, bn_cus_commodity.summary')
            ->join('INNER JOIN','bn_cus_commodity','bn_cus_commodity_favor.commodity_id = bn_cus_commodity.id')
            ->where(['bn_cus_commodity_favor.user_id' => $this->user['id'],'bn_cus_commodity_favor.status' => 1, 'bn_cus_commodity_favor.store_id' => $storeId]);

        $list = $query
            ->orderBy('id DESC')
            ->all();

        foreach ($list as &$v) {
            if (empty($v['summary'])) {
                $v['summary'] = '时价好物，实惠亲民';
            }
            // 只取第一张图片
            $v['pic']= explode(':;', $v['pic'])[0];
            // 查询可售卖的单位
            $cusCommodityProfile = CusCommodityProfile::find()->asArray()
                ->where(['commodity_id' => $v['id'], 'is_sell' => 1])
                ->one();
            $v['basic_unit'] = $v['unit'];
            $v['unit'] = $cusCommodityProfile['name'];
            $v['is_basics_unit'] = $cusCommodityProfile['is_basics_unit'];
            $v['price'] = $cusCommodityProfile['price'];
            $v['base_self_ratio'] = $cusCommodityProfile['base_self_ratio'];
        }

        return ['list' => $list, 'classData' => $query1->all()];
    }

    /**
     * 收藏商品
     * @param $commodity_id
     * @return mixed
     */
    public function favor($commodity_id, $storeId){
        $query = CusCommodityFavor::findOne(['commodity_id' => $commodity_id, 'user_id' => $this->user['id'], 'store_id' => $storeId]);
        if(!$query){
            $model = new CusCommodityFavor();
            $data = [
                'commodity_id' => $commodity_id,
                'store_id' => $storeId,
                'status' => 1,
                'user_id' => $this->user['id'],
                'create_time' => time()
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
    public function getFavorStatus($id, $userId){
        $model = CusCommodityFavor::findOne(['commodity_id'=>$id,'user_id' => $userId]);
        return isset($model->status) ? $model->status : 0;
    }

}
