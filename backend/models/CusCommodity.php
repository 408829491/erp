<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * This is the model class for table "cus_commodity".
 *
 * @property int $id ID
 * @property string $store_id 门店id
 * @property string $name 商品名称
 * @property int $type_first_tier_id 商品类型第一层ID(方便查询)
 * @property string $type_id 商品类型ID
 * @property string $price 市场价
 * @property string $in_price 进货价
 * @property string $unit 单位
 * @property string $summary 描述
 * @property int $is_online 是否上架
 * @property int $sell_num 销量
 * @property int $sequence 排序
 * @property int $provider_id 供应商ID
 * @property string $commodity_code 产品编码
 * @property string $is_rough 是否标品
 * @property string $pinyin 商品助记码
 * @property int $agent_id 采购员ID
 * @property int $parent_id 推荐人ID
 * @property string $alias 商品别名
 * @property int $channel_type 采购类型(0自采,1供应商供货)
 * @property string $tag 商品标签
 * @property int $is_time_price 是否时价
 * @property string $brand 商品品牌
 * @property string $product_place 商品产地
 * @property string $loss_rate 损耗率
 * @property string $durability_period 保质期
 * @property string $sell_stock 售卖库存
 * @property int $is_sell_stock 是否限制卖库存
 * @property int $unit_change_disabled 禁止修改单位
 * @property string $notice 详情描述
 * @property string $pic 图片
 * @property string $create_datetime 创建日期时间
 * @property string $modify_datetime 修改日期时间
 */
class CusCommodity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_cus_commodity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'type_first_tier_id', 'is_online','type_id', 'sell_num', 'sequence', 'provider_id', 'agent_id', 'parent_id', 'channel_type', 'is_time_price', 'is_sell_stock', 'unit_change_disabled'], 'integer'],
            [['name', 'type_id', 'commodity_code'], 'required'],
            [['price', 'in_price', 'loss_rate', 'sell_stock'], 'number'],
            [['is_rough', 'pic'], 'string'],
            [['create_datetime', 'modify_datetime'], 'safe'],
            [['name', 'tag'], 'string', 'max' => 255],
            [['notice'], 'string'],
            [['unit', 'commodity_code', 'pinyin', 'brand', 'durability_period'], 'string', 'max' => 50],
            [['summary'], 'string', 'max' => 999],
            [['alias'], 'string', 'max' => 100],
            [['product_place'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'name' => 'Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'price' => 'Price',
            'in_price' => 'In Price',
            'unit' => 'Unit',
            'summary' => 'Summary',
            'is_online' => 'Is Online',
            'sell_num' => 'Sell Num',
            'sequence' => 'Sequence',
            'provider_id' => 'Provider ID',
            'commodity_code' => 'Commodity Code',
            'is_rough' => 'Is Rough',
            'pinyin' => 'Pinyin',
            'agent_id' => 'Agent ID',
            'parent_id' => 'Parent ID',
            'alias' => 'Alias',
            'channel_type' => 'Channel Type',
            'tag' => 'Tag',
            'is_time_price' => 'Is Time Price',
            'brand' => 'Brand',
            'product_place' => 'Product Place',
            'loss_rate' => 'Loss Rate',
            'durability_period' => 'Durability Period',
            'sell_stock' => 'Sell Stock',
            'is_sell_stock' => 'Is Sell Stock',
            'unit_change_disabled' => 'Unit Change Disabled',
            'notice' => 'Notice',
            'pic' => 'Pic',
            'create_datetime' => 'Create Datetime',
            'modify_datetime' => 'Modify Datetime',
        ];
    }

    /**
     * 获取分页list数据
     * @param int $pageNum
     * @param int $pageSize
     * @param $filterProperty
     * @param $select
     * @return mixed
     */
    public function findPage($pageNum,$pageSize,$filterProperty,$select)
    {
        $pageNum -= 1;
        $query = self::find();

        // 添加筛选条件
        $storeId = Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => Yii::$app->user->identity['store_id']]);
        }

        if ($select != null) {
            $query->select([$select]);
        } else{
            $query->select(
                'id,is_online,name,pic,unit,price,alias,channel_type,notice,type_id,type_first_tier_id,brand,summary,tag'
            );
        }
        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $typeId = isset($json['typeId'])?$json['typeId']:null;
            if ($typeId != null) {
                $query->andWhere(['type_first_tier_id' => $typeId]);
            }
            $isOnline = isset($json['isOnline'])?$json['isOnline']:null;
            if ($isOnline != null) {
                $query->andWhere(['is_online' => $isOnline]);
            }
            $channelType = isset($json['channelType'])?$json['channelType']:null;
            if ($channelType != null) {
                $query->andWhere(['channel_type' => $channelType]);
            }
            $searchText = isset($json['searchText'])?$json['searchText']:null;
            if ($searchText != null) {
                $query->andWhere(['like', 'name', "%$searchText%", false]);
            }
            $ids = isset($json['ids'])?$json['ids']:null;
            if ($ids != null) {
                $query->andWhere(['not in', 'id', $ids, false]);
            }
        }
        $keyword = YII::$app->request->get('keyword');
        if ($keyword) {
            $query->andWhere(['like', 'name', "%$keyword%", false]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        $commodityCategory = $this->getCategoryList();
        foreach ($tempData as &$value) {
            // 格式化分类名称（主分类/子分类）
            $id = $value['type_id'];
            $value['type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['name'] : "";
            $value['parent_type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['parent_name'] : "";
            $value['pic']= explode(':;',$value['pic'])[0];
            $value['num'] = 1;
            $value['remark'] = '';
            $value['total_price']=0.00;
        }
        $data['list'] = $tempData;
        return $data;
    }

    /**
     * 获取格式化分类数据
     * @return array
     */
    public function getCategoryList(){
        $commodityCategory = CusCommodityCategory::find()
            ->select('id,name,pid')
            ->asArray()
            ->all();//获取所有分类
        $categoryIndex = array_column($commodityCategory,'name','id'); //生成商分类Map
        $categoryIndexList = array();
        //格式化数组
        foreach($commodityCategory as $k=>$v){
            $categoryIndexList[$v['id']]['id'] = $v['id'];
            $categoryIndexList[$v['id']]['name'] = $v['name'];
            $categoryIndexList[$v['id']]['parent_name'] = ($v['pid'] == 0)?'顶级分类':$categoryIndex[$v['pid']];
        }
        return $categoryIndexList;
    }

    // 保存商品
    public function saveData($model, $unitLit) {
        $transaction  = CusCommodity::getDb()->beginTransaction();

        try {

            $model->save();
            // 保存子表
            $this->saveSubUnitList($model, $unitLit);

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 根据json保存子表
    public function saveSubUnitList($model,$unitLit) {
        for ( $i = 0; $i < sizeof($unitLit); $i ++) {
            $item = json_decode($unitLit[$i]);
            $subItem = new CusCommodityProfile();
            $subItem->commodity_id = $model->id;
            $subItem->name = $item->unit_unit;
            $subItem->price = $item->unit_price;
            $subItem->desc = $item->unit_desc;
            $subItem->is_basics_unit = $item->unit_is_basics_unit;
            $subItem->base_self_ratio = $item->unit_base_self_ratio;
            //$subItem->tag = $item->tag;
            $subItem->is_sell = $item->unit_is_sell ? '1' : '0';
            if (!$subItem->validate()) {
                $error = $subItem->errors;
                throw new Exception(implode(",", $subItem->errors));
            }
            $subItem->save();
        }
    }

    // 删除子表
    public function deleteSubUnitList($model) {
        CusCommodityProfile::deleteAll(['commodity_id'=>$model->id]);
    }

    // 修改商品保存
    public function edit($model, $unitLit) {
        $transaction  = CusCommodity::getDb()->beginTransaction();

        try {

            $model->save();
            // 删除相关子表
            $this->deleteSubUnitList($model);
            // 保存子表
            $this->saveSubUnitList($model, $unitLit);

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 查询商品的所有单位
    public function findCommodityUnitDataList($pageNum,$pageSize,$filterProperty,$select) {
        $pageNum -= 1;
        $query = self::find()
            ->leftJoin('bn_cus_commodity_profile', 'bn_cus_commodity_profile.commodity_id = bn_cus_commodity.id');

        // 添加筛选条件
        $storeId = Yii::$app->request->get('storeId');
        if ($storeId != null) {
            $query->where(['store_id' => $storeId]);
        } else {
            $query->where(['store_id' => Yii::$app->user->identity['store_id']]);
        }

        if ($select != null) {
            $query->select([$select]);
        } else{
            $query->select(
                'bn_cus_commodity.id,bn_cus_commodity.is_online,bn_cus_commodity.name,bn_cus_commodity.pic,bn_cus_commodity_profile.name as unit,bn_cus_commodity_profile.price,bn_cus_commodity.alias,bn_cus_commodity.channel_type,bn_cus_commodity.notice,bn_cus_commodity.type_id,bn_cus_commodity.type_first_tier_id,bn_cus_commodity.brand,bn_cus_commodity.summary,bn_cus_commodity.tag'
            );
        }
        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $typeId = isset($json['typeId'])?$json['typeId']:null;
            if ($typeId != null) {
                $query->andWhere(['bn_cus_commodity.type_first_tier_id' => $typeId]);
            }
            $isOnline = isset($json['isOnline'])?$json['isOnline']:null;
            if ($isOnline != null) {
                $query->andWhere(['bn_cus_commodity.is_online' => $isOnline]);
            }
            $channelType = isset($json['channelType'])?$json['channelType']:null;
            if ($channelType != null) {
                $query->andWhere(['bn_cus_commodity.channel_type' => $channelType]);
            }
            $searchText = isset($json['searchText'])?$json['searchText']:null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_cus_commodity.name', "%$searchText%", false]);
            }
            $ids = isset($json['ids'])?$json['ids']:null;
            if ($ids != null) {
                $query->andWhere(['not in', 'bn_cus_commodity.id', $ids, false]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();

        $commodityCategory = $this->getCategoryList();
        foreach ($tempData as &$value) {
            // 格式化分类名称（主分类/子分类）
            $id = $value['type_id'];
            $value['type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['name'] : "";
            $value['parent_type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['parent_name'] : "";
            $value['pic']= explode(':;',$value['pic'])[0];
            $value['num'] = 1;
            $value['remark'] = '';
            $value['total_price']=0.00;
        }
        $data['list'] = $tempData;
        return $data;
    }
}
