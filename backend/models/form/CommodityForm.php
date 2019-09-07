<?php
/**
 * Created by PhpStorm.
 * User: xiaomage
 * Date: 2019/4/28
 * Time: 10:03
 */

namespace app\models\form;

use app\models\Commodity;
use app\models\CommodityCategory;
use app\models\CommodityProfileDetail;
use app\models\Model;
use Yii;
use yii\data\Pagination;

class CommodityForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $searchText;
    public $typeId;
    public $type_first_tier_id;
    public $ids;
    public $set;


    public function rules()
    {
        return [
            [['searchText',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['typeId',], 'default', 'value' => ''],
            [['ids',], 'default', 'value' => ''],
            [['set',], 'default', 'value' => 0],
            [['type_first_tier_id',], 'default', 'value' => ''],
        ];
    }

    /**
     * 获取商品数据
     * @return mixed
     */
    public function getCommodityData()
    {
        $this->attributes = Yii::$app->request->get();
        $query = Commodity::find();
        $query->where(['is_online' => 1]);
        if ($this->searchText) {
            $query->andWhere([
                'or',
                ['like', 'bn_commodity.name', $this->searchText],
                ['like', 'alias', $this->searchText],
            ]);
        }
        if ($this->typeId) {
            $query->andwhere(['type_first_tier_id' => $this->typeId]);
        }

        if ($this->ids) {
            $query->andWhere(['not in', 'concat_ws("-",bn_commodity.id,bn_commodity_profile.name)', $this->ids]);
        }
        $query->select('bn_commodity.id,bn_commodity.id as commodity_id,bn_commodity_profile.id as pid,bn_commodity_profile.stock_limit_up_num,bn_commodity_profile.stock_limit_down_num,bn_commodity_profile.stock_num as sell_stock,bn_commodity.channel_type,bn_commodity.name,bn_commodity_profile.id as pid,bn_commodity_profile.in_price,type_id,type_first_tier_id,pic,bn_commodity_profile.name as unit,bn_commodity_profile.price,agent_id,agent_name,notice,bn_commodity_profile.is_setting_formula')->leftJoin('bn_commodity_profile', 'bn_commodity_profile.commodity_id=bn_commodity.id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $data = $query
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->orderBy('bn_commodity.id DESC')
            ->asArray()
            ->all();
        $userTypePriceInfo = [];
        $commodityIds = array_values(array_column($data, 'id'));

            $userTypePriceInfo = $this->getCommodityPriceInfo($commodityIds);

        $commodityCategory = $this->getCategoryList();
        foreach ($data as $key => &$value) {
            //格式化分类名称（主分类/子分类）
            $id = $value['type_id'];
            $value['type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['name'] : "";
            $value['parent_type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['parent_name'] : "";
            $value['pic'] = explode(':;', $value['pic'])[0];
            $value['num'] = 1;
            $value['priceInfo'] = isset($userTypePriceInfo[$value['id']][$value['unit']])?$userTypePriceInfo[$value['id']][$value['unit']]:'';
            $value['remark'] = '';
            $value['total_price'] = 0.00;
        }

        return [
            'total' => $count,
            'list' => $data,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }


    /**
     * 获取所有客户类型商品价格
     * @param array $ids
     * @return array
     */
    public function getCommodityPriceInfo($ids = [])
    {
        $model = CommodityProfileDetail::find();
        $query = $model->select('bn_commodity_profile_detail.*,bn_commodity_profile.name,base_self_ratio')
            ->leftJoin('bn_commodity_profile', 'bn_commodity_profile.id = bn_commodity_profile_detail.commodity_profile_id')
            ->where(['in', 'bn_commodity_profile_detail.commodity_id', $ids])
            ->asArray()
            ->all();
        $princeInfo = [];
        foreach ($query as $k => $v) {
            $princeInfo[$v['commodity_id']][$v['name']][$k]['id'] = $v['id'];
            $princeInfo[$v['commodity_id']][$v['name']][$k]['price'] = $v['price'];
            $princeInfo[$v['commodity_id']][$v['name']][$k]['name'] = $v['name'];
            $princeInfo[$v['commodity_id']][$v['name']][$k]['base_self_ratio'] = $v['base_self_ratio'];
            $princeInfo[$v['commodity_id']][$v['name']][$k]['type_id'] = $v['type_id'];
        }
        return $princeInfo;
    }

    /**
     * 获取格式化分类数据
     * @return array
     */
    public function getCategoryList()
    {
        $commodityCategory = CommodityCategory::find()
            ->select('id,name,pid')
            ->asArray()
            ->all();//获取所有分类
        $categoryIndex = array_column($commodityCategory, 'name', 'id'); //生成商分类Map
        $categoryIndexList = array();
        //格式化数组
        foreach ($commodityCategory as $k => $v) {
            $categoryIndexList[$v['id']]['id'] = $v['id'];
            $categoryIndexList[$v['id']]['name'] = $v['name'];
            $categoryIndexList[$v['id']]['parent_name'] = ($v['pid'] == 0) ? '顶级分类' : $categoryIndex[$v['pid']];
        }
        return $categoryIndexList;
    }

    /**
     * 获取订单历史
     * @return array
     */

    public function getSettingPriceHistory(){
      return [];
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval, $timestamp = 1)
    {
        $date_interval = explode(' - ', $date_interval);
        return [
            'begin_date' => $timestamp ? strtotime($date_interval[0]) : $date_interval[0],
            'end_date' => $timestamp ? strtotime($date_interval[1]) : $date_interval[1]
        ];
    }
}