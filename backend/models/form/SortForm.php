<?php

namespace app\models\form;

use app\models\CommodityCategory;
use app\models\Customer;
use app\models\Model;
use app\models\OrderDetail;
use app\models\SortSorter;
use Yii;
use yii\data\Pagination;
use yii\db\Expression;

class SortForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $searchText;
    public $area_name;
    public $is_check;
    public $is_sorted;
    public $create_time;
    public $delivery_date;
    public $password;
    public $type_first_tier_id;
    public $role=[
            '10'=>'分拣员'
        ];
    public function rules()
    {
        return [
            [['searchText',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['area_name',], 'default', 'value' => '',],
            [['create_time',], 'default', 'value' => ''],
            [['delivery_date',], 'default', 'value' => ''],
            [['is_sorted',], 'default', 'value' => ''],
            [['type_first_tier_id',], 'default', 'value' => ''],
            [['password',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询需要分拣的列表
     * @return array
     */
    public function search($filterProperty)
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = OrderDetail::find()
            ->select('bn_order_detail.*,bn_order.nick_name, bn_order.status')
            ->join('LEFT JOIN','bn_order','bn_order.id=order_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
            $provider_id = isset($json['provider_id']) ? $json['provider_id'] : null;
            if ($provider_id != null) {
                $query->andWhere(['bn_order_detail.provider_id' => $provider_id]);
            }
            $agent_id = isset($json['agent_id']) ? $json['agent_id'] : null;
            if ($agent_id != null) {
                $query->andWhere(['bn_order_detail.agent_id' => $agent_id]);
            }
            $is_sorted = isset($json['is_sorted']) ? $json['is_sorted'] : null;
            if ($is_sorted != null) {
                $query->andWhere(['bn_order_detail.is_sorted' => $is_sorted]);
            }
            $type_first_tier_id = isset($json['type_first_tier_id']) ? $json['type_first_tier_id'] : null;
            if ($type_first_tier_id != null) {
                $query->andWhere(['bn_order_detail.type_first_tier_id' => $type_first_tier_id]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order.nick_name', "%$searchText%", false]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('is_sorted, id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as  &$v){
            $v['sorter'] = Yii::$app->user->identity['nickname'];
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 更新分拣状态
     * @param $id
     * @param $amount
     * @return mixed
     */
    public function changeStatus($id,$amount)
    {
        $model = OrderDetail::findOne($id);
        if($model->is_sorted === 1){
            return false;
        }
        $model->actual_num = $amount;
        $model->is_sorted = 1;
        $model->sort_id = Yii::$app->user->identity['id'];
        $model->sort_name = Yii::$app->user->identity['nickname'];
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }



    /**
     * 保存/新增客户信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0)
    {
        $model = $id ? Customer::findOne($id) : new Customer();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        $model->generateAuthKey();
        if (!$id) {
            $model->setPassword($this->password);
            $model->created_ip = Yii::$app->getRequest()->getUserIP();
            $model->created_at = time();
            $model->contact_name = $model->nickname;
            $model->mobile = $model->username;
            $model->status = 10;
            $model->r_id = 10;
        }else{
            if (!empty($this->password)) $model->setPassword($this->password);
        }
        if ($model->save()) {
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }

    public function sortRateData($filterProperty)
    {
        $this->attributes = Yii::$app->request->get();
        $query = OrderDetail::find()->asArray()
            ->select(['commodity_id, commodity_name,unit, is_basics_unit, base_unit, base_self_ratio, notice, count(0) as totalNum','sum(if(is_sorted = 1, 1, 0)) as sortedNum'])
            ->groupBy('commodity_id,is_basics_unit,unit,base_self_ratio');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order_detail.commodity_name', "%$searchText%", false]);
            }
            $sortStatus = isset($json['sortStatus']) ? $json['sortStatus'] : null;
            if ($sortStatus != null) {
                if ($sortStatus == 0) {
                    // 未分拣
                    $query->having(['sortedNum' => 0]);
                } else if ($sortStatus == 1) {
                    // 分拣中
                    $query->having('sortedNum < totalNum and sortedNum != 0');
                } else {
                    // 已分拣
                    $query->having('sortedNum = totalNum');
                }
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->orderBy('commodity_id DESC')
            ->limit($pagination->pageSize)
            ->all();
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    public function sortRateByUserData($filterProperty)
    {
        $this->attributes = Yii::$app->request->get();
        $query = OrderDetail::find()->asArray()
            ->select(['bn_order.user_id, bn_order.nick_name, bn_order.user_name, bn_order.line_id, bn_order.line_name, count(0) as totalNum','sum(if(bn_order_detail.is_sorted = 1, 1, 0)) as sortedNum'])
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->groupBy('bn_order.user_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order.nick_name', "%$searchText%", false]);
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
            $sortStatus = isset($json['sortStatus']) ? $json['sortStatus'] : null;
            if ($sortStatus != null) {
                if ($sortStatus == 0) {
                    // 未分拣
                    $query->having(['sortedNum' => 0]);
                } else if ($sortStatus == 1) {
                    // 分拣中
                    $query->having('sortedNum < totalNum and sortedNum != 0');
                } else {
                    // 已分拣
                    $query->having('sortedNum = totalNum');
                }
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->orderBy('user_id DESC')
            ->limit($pagination->pageSize)
            ->all();
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 核算一键打印分拣数量
     * @return mixed
     */
    public function checkOneTouchPrint(){
        $this->attributes = Yii::$app->request->post();
        $query = OrderDetail::find()->where(['is_sorted'=>0]);
        if ($this->delivery_date) {
            $query->andwhere(['delivery_date' => $this->delivery_date]);
        }
        if ($this->is_sorted) {
            $query->andwhere(['is_sorted' => $this->is_sorted]);
        }
        if ($this->searchText) {
            $query->andwhere(['is_sorted' => $this->is_sorted]);
        }
        if ($this->type_first_tier_id) {
            $query->andwhere(['type_first_tier_id' => $this->type_first_tier_id]);
        }
        $ids = array_column($query->asArray()->all(),'id');
        return ['count' =>$query->count(),'ids'=>join(',',$ids)];
    }

    // 查找需要分拣的分类及商品
    public function findClassAndProduct($filterProperty) {
        $query = OrderDetail::find()->asArray()
            ->select('type_first_tier_id, type_id, parent_type_name, type_name')
            ->groupBy('type_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
            $provider_id = isset($json['provider_id']) ? $json['provider_id'] : null;
            if ($provider_id != null) {
                $query->andWhere(['bn_order_detail.provider_id' => $provider_id]);
            }
            $agent_id = isset($json['agent_id']) ? $json['agent_id'] : null;
            if ($agent_id != null) {
                $query->andWhere(['bn_order_detail.agent_id' => $agent_id]);
            }
            $is_sorted = isset($json['is_sorted']) ? $json['is_sorted'] : null;
            if ($is_sorted != null) {
                $query->andWhere(['bn_order_detail.is_sorted' => $is_sorted]);
            }
            $type_first_tier_id = isset($json['type_first_tier_id']) ? $json['type_first_tier_id'] : null;
            if ($type_first_tier_id != null) {
                $query->andWhere(['bn_order_detail.type_first_tier_id' => $type_first_tier_id]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order_detail.commodity_name', "%$searchText%", false]);
            }
        }

        $data = $query->all();

        // 组装数据
        $firstTierClassList = [];
        foreach ($data as $item) {
            // 查询是否已经存在一级分类
            $isExistFirstTierClassId = false;
            foreach ($firstTierClassList as $item2) {
                if ($item2['type_first_tier_id'] == $item['type_first_tier_id']) {
                    $isExistFirstTierClassId = true;
                    break;
                }
            }

            if (!$isExistFirstTierClassId) {
                $firstTierClass = [];
                $firstTierClass['type_first_tier_id'] = $item['type_first_tier_id'];
                $firstTierClass['parent_type_name'] = $item['parent_type_name'];
                array_push($firstTierClassList, $firstTierClass);
            }
        }

        return ['firstTierClassList' => $firstTierClassList, 'secondTierAndDataList' => $data];
    }

    // 根据二级分类查找数据
    public function findDataBySecondTier($filterProperty) {
        $query = OrderDetail::find()->asArray()
            ->select(['bn_order_detail.*', 'count(0) as totalNum', 'sum(if(bn_order_detail.is_sorted = 1, 1, 0)) as sortedNum'])
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->groupBy('bn_order_detail.commodity_id')
            ->andWhere(['bn_order.status' => 1]);

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $is_sorted = isset($json['is_sorted']) ? $json['is_sorted'] : null;
            if ($is_sorted != null) {
                $query->andWhere(['bn_order_detail.is_sorted' => $is_sorted]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order_detail.commodity_name', "%$searchText%", false]);
            }
            $typeId = isset($json['type_id']) ? $json['type_id'] : null;
            if ($typeId != null) {
                $query->andWhere(['bn_order_detail.type_id' => $typeId]);
            }
        }

        $data = $query->all();

        return $data;
    }

    // 根据一级分类查找数据
    public function findDataByFirstTier($filterProperty) {
        $query = OrderDetail::find()->asArray()
            ->select('type_first_tier_id, type_id, parent_type_name, type_name')
            ->groupBy('type_id');

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $line_id = isset($json['line_id']) ? $json['line_id'] : null;
            if ($line_id != null) {
                $query->andWhere(['bn_order.line_id' => $line_id]);
            }
            $provider_id = isset($json['provider_id']) ? $json['provider_id'] : null;
            if ($provider_id != null) {
                $query->andWhere(['bn_order_detail.provider_id' => $provider_id]);
            }
            $agent_id = isset($json['agent_id']) ? $json['agent_id'] : null;
            if ($agent_id != null) {
                $query->andWhere(['bn_order_detail.agent_id' => $agent_id]);
            }
            $is_sorted = isset($json['is_sorted']) ? $json['is_sorted'] : null;
            if ($is_sorted != null) {
                $query->andWhere(['bn_order_detail.is_sorted' => $is_sorted]);
            }
            $type_first_tier_id = isset($json['type_first_tier_id']) ? $json['type_first_tier_id'] : null;
            if ($type_first_tier_id != null) {
                $query->andWhere(['bn_order_detail.type_first_tier_id' => $type_first_tier_id]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order_detail.commodity_name', "%$searchText%", false]);
            }
            $typeFirstTierId = isset($json['type_first_tier_id']) ? $json['type_first_tier_id'] : null;
            if ($typeFirstTierId != null) {
                $query->andWhere(['bn_order_detail.type_first_tier_id' => $typeFirstTierId]);
            }
        }

        $data = $query->all();

        return $data;
    }

    // 全屏分拣详情
    public function fullScreenSortDetail($commodityId, $deliveryDate, $isSorted) {
        $query = OrderDetail::find()->asArray()
            ->select('bn_order_detail.*, bn_order.nick_name')
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->where(['bn_order_detail.commodity_id' => $commodityId])
            ->andWhere(['bn_order.status' => 1]);

        // 添加查询条件
        if ($deliveryDate != null && $deliveryDate != '') {
            $query->andWhere(['bn_order_detail.delivery_date' => $deliveryDate]);
        }
        if ($isSorted != null && $isSorted != '' && $isSorted != '-1') {
            $query->andWhere(['bn_order_detail.is_sorted' => $isSorted]);
        }

        // 添加排序条件
        $query->orderBy('bn_order_detail.is_sorted');

        $data = $query->all();

        return $data;
    }

    // 按客户全屏分拣详情
    public function fullScreenSortByUserDetail($userId, $deliveryDate, $isSorted) {
        $query = OrderDetail::find()->asArray()
            ->select('bn_order_detail.*, bn_order.nick_name')
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->where(['bn_order.user_id' => $userId])
            ->andWhere(['bn_order.status' => 1]);

        // 添加查询条件
        if ($deliveryDate != null && $deliveryDate != '') {
            $query->andWhere(['bn_order_detail.delivery_date' => $deliveryDate]);
        }
        if ($isSorted != null && $isSorted != '' && $isSorted != '-1') {
            $query->andWhere(['bn_order_detail.is_sorted' => $isSorted]);
        }

        // 添加排序条件
        $query->orderBy('bn_order_detail.is_sorted');

        $data = $query->all();

        return $data;
    }

    // 查询一键分拣需要分拣的商品数量
    public function findSortAllNumByFirstTierClassAndDate($bigClassId, $date) {
        $query = OrderDetail::find()->asArray()
            ->andWhere(['delivery_date' => $date])
            ->andWhere(['is_sorted' => 0]);

        if ($bigClassId != -1) {
            $query->andWhere(['type_first_tier_id' => $bigClassId]);
        }

        $data = $query->all();
        return count($data);
    }

    // 一键分拣需要分拣的商品根据日期跟商品Id
    public function findSortNumAllByDateAndCommodityId($date, $commodityId) {
        $query = OrderDetail::find()->asArray()
            ->andWhere(['delivery_date' => $date])
            ->andWhere(['is_sorted' => 0])
            ->andWhere(['commodity_id' => $commodityId]);

        $data = $query->all();
        $totalNum = count($data);
        if ($totalNum != 0) {
            // 计算ids
            $ids = '';
            foreach ($data as $item) {
                $ids = $ids.$item['id'].',';
            }
            if ($ids != '') {
                $ids = substr($ids, 0, strlen($ids) - 1);
            }
        }
        return ['totalNum' => $totalNum, 'ids' => $ids];
    }

    // 一键分拣查询按客户跟日期
    public function findSortNumAllByDateAndUserId($date, $userId) {
        $query = OrderDetail::find()->asArray()
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->andWhere(['bn_order_detail.delivery_date' => $date])
            ->andWhere(['bn_order_detail.is_sorted' => 0])
            ->andWhere(['bn_order.user_id' => $userId]);

        $data = $query->all();
        $totalNum = count($data);
        $ids = '';
        if ($totalNum != 0) {
            // 计算ids
            $ids = '';
            foreach ($data as $item) {
                $ids = $ids.$item['id'].',';
            }
            if ($ids != '') {
                $ids = substr($ids, 0, strlen($ids) - 1);
            }
        }

        return ['totalNum' => $totalNum, 'ids' => $ids];
    }

    // 查找订单通过客户聚合
    public function findOrderDetailByUser($filterProperty) {
        $query = OrderDetail::find()->asArray()
            ->select(['bn_order.user_id', 'bn_order.nick_name', 'count(0) as totalNum', 'sum(if(bn_order_detail.is_sorted = 1, 1, 0)) as sortedNum'])
            ->leftJoin('bn_order', 'bn_order.id = bn_order_detail.order_id')
            ->groupBy('bn_order.user_id')
            ->andWhere(['bn_order.status' => 1]);

        // 添加查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $delivery_date = isset($json['delivery_date']) ? $json['delivery_date'] : null;
            if ($delivery_date != null) {
                $query->andWhere(['bn_order_detail.delivery_date' => $delivery_date]);
            }
            $is_sorted = isset($json['is_sorted']) ? $json['is_sorted'] : null;
            if ($is_sorted != null) {
                $query->andWhere(['bn_order_detail.is_sorted' => $is_sorted]);
            }
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'bn_order.nick_name', "%$searchText%", false]);
            }
            $typeId = isset($json['type_id']) ? $json['type_id'] : null;
            if ($typeId != null) {
                $query->andWhere(['bn_order_detail.type_id' => $typeId]);
            }
        }

        $data = $query->all();

        return $data;
    }
}
