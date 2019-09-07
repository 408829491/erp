<?php

namespace app\models;

use common\models\User;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * This is the model class for table "bn_commodity".
 *
 * @property int $id ID
 * @property string $name 商品名称
 * @property int $type_first_tier_id 商品类型第一层ID(方便查询)
 * @property int $type_id 商品类型ID
 * @property string $price 市场价
 * @property string $in_price 进货价
 * @property string $activity_price 活动价格(辅助)
 * @property int $is_seckill 是否秒杀商品
 * @property string $unit 单位
 * @property string $summary 描述
 * @property int $is_online 是否上架
 * @property int $sell_num 销量
 * @property int $sequence 排序
 * @property string $category 类别组(弃)
 * @property int $category_id 大类(弃)
 * @property int $category_id2 子类(弃)
 * @property string $rule_status
 * @property int $order_quantity 起订量
 * @property int $max_quantity 最大订购数理
 * @property int $provider_id 供应商ID
 * @property string $provider_name 供应商名称
 * @property int $agent_id 采购员ID
 * @property string $agent_name 采购员姓名
 * @property string $is_active 是否激活
 * @property string $commodity_code 产品编码
 * @property string $is_rough 是否标品
 * @property string $unit_convert 是否转换单位
 * @property string $unit_sell 转换单位
 * @property string $unit_num 单位计量
 * @property string $pinyin 商品助记码
 * @property int $parent_id 推荐人ID
 * @property int $is_process
 * @property string $alias 商品别名
 * @property string $status 状态
 * @property int $hide 是否
 * @property int $channel_type 采购类型(0自采,1供应商供货)
 * @property string $tag 商品标签
 * @property int $allow_change_channel
 * @property int $is_time_price 是否时价
 * @property string $brand 商品品牌
 * @property string $product_place 商品产地
 * @property string $loss_rate 损耗率
 * @property string $durability_period 保质期
 * @property int $sell_stock 售卖库存
 * @property int $is_sell_stock 是否限制卖库存
 * @property int $is_setting_formula 是否设置定价公式
 * @property string $stock_limit_up_num 库存上限
 * @property string $stock_limit_down_num 库存下限
 * @property int $product_line_id 产品线路id
 * @property int $unit_change_disabled 禁止修改单位
 * @property string $notice 详情描述
 * @property int $create_time 创建时间
 * @property int $modify_time 更新时间
 * @property int $delete_time 删除时间
 * @property string $pic 图片
 * @property int $stock_position 库位
 */
class Commodity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bn_commodity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type_id', 'commodity_code'], 'required'],
            [['type_first_tier_id', 'type_id', 'is_seckill', 'is_online', 'sell_num', 'sequence', 'category_id', 'category_id2', 'order_quantity', 'max_quantity', 'provider_id', 'agent_id', 'parent_id', 'is_process', 'hide', 'channel_type', 'allow_change_channel', 'is_time_price', 'sell_stock', 'is_sell_stock', 'product_line_id', 'unit_change_disabled', 'create_time', 'modify_time', 'delete_time', 'stock_position','is_setting_formula'], 'integer'],
            [['price', 'in_price', 'activity_price', 'unit_num', 'loss_rate', 'stock_limit_up_num', 'stock_limit_down_num'], 'number'],
            [['rule_status', 'is_active', 'is_rough', 'unit_convert', 'status', 'pic'], 'string'],
            [['name', 'category', 'tag', 'notice'], 'string', 'max' => 255],
            [['unit', 'provider_name', 'agent_name', 'commodity_code', 'pinyin', 'brand', 'durability_period'], 'string', 'max' => 50],
            [['summary'], 'string', 'max' => 999],
            [['unit_sell'], 'string', 'max' => 10],
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
            'name' => 'Name',
            'type_first_tier_id' => 'Type First Tier ID',
            'type_id' => 'Type ID',
            'price' => 'Price',
            'in_price' => 'In Price',
            'activity_price' => 'Activity Price',
            'is_seckill' => 'Is Seckill',
            'unit' => 'Unit',
            'summary' => 'Summary',
            'is_online' => 'Is Online',
            'sell_num' => 'Sell Num',
            'sequence' => 'Sequence',
            'category' => 'Category',
            'category_id' => 'Category ID',
            'category_id2' => 'Category Id2',
            'rule_status' => 'Rule Status',
            'order_quantity' => 'Order Quantity',
            'max_quantity' => 'Max Quantity',
            'provider_id' => 'Provider ID',
            'provider_name' => 'Provider Name',
            'agent_id' => 'Agent ID',
            'agent_name' => 'Agent Name',
            'is_active' => 'Is Active',
            'commodity_code' => 'Commodity Code',
            'is_rough' => 'Is Rough',
            'unit_convert' => 'Unit Convert',
            'unit_sell' => 'Unit Sell',
            'unit_num' => 'Unit Num',
            'pinyin' => 'Pinyin',
            'parent_id' => 'Parent ID',
            'is_process' => 'Is Process',
            'alias' => 'Alias',
            'status' => 'Status',
            'hide' => 'Hide',
            'channel_type' => 'Channel Type',
            'tag' => 'Tag',
            'allow_change_channel' => 'Allow Change Channel',
            'is_time_price' => 'Is Time Price',
            'brand' => 'Brand',
            'product_place' => 'Product Place',
            'loss_rate' => 'Loss Rate',
            'durability_period' => 'Durability Period',
            'sell_stock' => 'Sell Stock',
            'is_sell_stock' => 'Is Sell Stock',
            'stock_limit_up_num' => 'Stock Limit Up Num',
            'stock_limit_down_num' => 'Stock Limit Down Num',
            'product_line_id' => 'Product Line ID',
            'unit_change_disabled' => 'Unit Change Disabled',
            'notice' => 'Notice',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'delete_time' => 'Delete Time',
            'pic' => 'Pic',
            'stock_position' => 'Stock Position',
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
    public function findPage($pageNum = 0, $pageSize = 10, $filterProperty, $select)
    {

        $pageNum -= 1;
        $query = self::find();

        if ($select != null) {
            $query->select([$select]);
        } else {
            $query->select(
                'id,is_online,name,pic,unit,price,in_price,alias,channel_type,notice,type_id,type_first_tier_id,brand,summary,stock_position,sell_stock,agent_id,agent_name,is_setting_formula,stock_limit_up_num,stock_limit_down_num'
            );
        }

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty, true);
            $typeId = isset($json['typeId']) ? $json['typeId'] : null;
            if ($typeId != null) {
                $query->andWhere(['type_first_tier_id' => $typeId]);
            }
            $isOnline = isset($json['isOnline']) ? $json['isOnline'] : null;
            if ($isOnline != null) {
                $query->andWhere(['is_online' => $isOnline]);
            }
            $channelType = isset($json['channelType']) ? $json['channelType'] : null;
            if ($channelType != null) {
                $query->andWhere(['channel_type' => $channelType]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'name', "%$searchText%", false]);
            }
            $ids = isset($json['ids']) ? $json['ids'] : null;
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
        $userId = isset($json['user_id']) ? $json['user_id'] : 0;
        $commodityIds = array_values(array_column($tempData, 'id'));
        $userTypePriceInfo=[];
        if ($userId) {
            $userInfo = User::findIdentity($userId);
            $userTypePrice = $this->getUserTypePrice($commodityIds, $userInfo['c_type_id']);
        }
        foreach ($tempData as &$value) {
            //格式化分类名称（主分类/子分类）
            $id = $value['type_id'];
            $value['type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['name'] : "";
            $value['parent_type_name'] = isset($commodityCategory[$id]) ? $commodityCategory[$id]['parent_name'] : "";
            $value['pic'] = explode(':;', $value['pic'])[0];
            if (isset($userTypePrice)) {
                $value['price'] = $userTypePrice[$value['id']];
            }
            $value['num'] = 1;
            $value['remark'] = '';
            $value['total_price'] = 0.00;
        }
        $data['list'] = $tempData;
        return $data;
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
     * 获取客户单个类型商品价格
     * @param $commodityIds
     * @param $type
     * @return array
     */
    public function getUserTypePrice($commodityIds, $type)
    {
        $model = CommodityProfileDetail::find();
        $data = $model
            ->select('bn_commodity_profile_detail.commodity_id,bn_commodity_profile_detail.price')
            ->leftJoin('bn_commodity_profile', 'bn_commodity_profile.id = bn_commodity_profile_detail.commodity_profile_id')
            ->where(['type_id' => $type, 'bn_commodity_profile_detail.commodity_id' => $commodityIds])
            ->asArray()
            ->all();
        return array_column($data, 'price', 'commodity_id');
    }



    // 保存商品
    public function saveData($model, $unitLit)
    {
        $transaction = Commodity::getDb()->beginTransaction();

        try {

            $model->save();
            // 保存子表
            $this->saveSubUnitList($model, $unitLit);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // 根据json保存子表
    private function saveSubUnitList($model, $unitList)
    {
        for ($i = 0; $i < sizeof($unitList); $i++) {
            $item = json_decode($unitList[$i]);
            $subItem = new CommodityProfile();
            $subItem->commodity_id = $model->id;
            $subItem->price = $item->price;
            $subItem->in_price = $item->in_price;
            $subItem->name = $item->unit_unit;
            $subItem->desc = $item->unit_desc;
            $subItem->is_basics_unit = $item->unit_is_basics_unit;
            $subItem->base_self_ratio = $item->unit_base_self_ratio;
            $subItem->is_sell = $item->unit_is_sell ? '1' : '0';
            if (!$subItem->validate()) {
                throw new Exception(implode(",", $subItem->errors));
            }
            $subItem->save();

            // 保存价格子表
            $this->saveSubUnitPriceList($subItem, $item->priceList);
        }
    }

    // 保存单位价格明细
    private function saveSubUnitPriceList($subModel, $priceList)
    {
        $items = json_decode($priceList);
        for ($i = 0; $i < count($items); $i++) {
            $model = new CommodityProfileDetail();
            $model->commodity_id = $subModel->commodity_id;
            $model->commodity_profile_id = $subModel->id;
            $model->type_id = $items[$i]->type;
            $model->price = $items[$i]->price;

            if (!$model->validate()) {
                throw new Exception(implode(",", $model->errors));
            }
            $model->save();
        }
    }

    // 删除子表
    public function deleteSubUnitList($model)
    {
        CommodityProfile::deleteAll(['commodity_id' => $model->id]);
    }

    // 修改商品保存
    public function edit($model, $unitLit)
    {
        $transaction = Commodity::getDb()->beginTransaction();

        try {

            $model->save();
            // 删除相关子表
            $this->deleteSubUnitList($model);
            // 保存子表
            $this->saveSubUnitList($model, $unitLit);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * 更新商品进价
     * @param $id
     * @param $price
     * @return bool
     */
    public function changeInPrice($id, $price)
    {
        $model = CommodityProfile::findOne($id);
        $model->in_price = $price;
        return $model->save();
    }
}
