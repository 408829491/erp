<?php

namespace app\models\form;

use app\models\CommodityCategory;
use app\models\CommodityDemand;
use Yii;
use yii\data\Pagination;

class CommodityDemandForm extends \yii\base\Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $create_time;
    public $user;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['create_time',], 'default','value'=>''],
        ];
    }

    /**
     * 查询需求列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return [];
        }
        $query = CommodityDemand::find();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->asArray()
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as &$v){
            $v['create_time'] = $this->dateFormat($v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $query->createCommand()->getRawSql()
        ];
    }

    /**
     * 保存商品需求
     * @param $commodity_id
     * @return mixed
     */
    public function save(){
        $model= new CommodityDemand();
        $model->attributes = Yii::$app->request->post();//属性赋值
        $model->user_id = $this->user['id'];
        $model->user_name = $this->user['username'];
        $model->create_time = time();
        if (!$model->validate()) {
            return ['code' => 400, 'msg' => '数据验证失败', 'data' => $model->getErrors()];
        }
        if($res = $model->save()){
            return $res;
        }
        return ['code'=>400,'msg'=>'数据保存失败','data'=>[]];
    }

    /**
     * 转换日期区间
     * @param string $date_interval
     * @param int $timestamp
     * @return array
     */
    public function dateFormat($date_interval,$timestamp = 1){
        $dates = explode(' - ',$date_interval);
        if(count($dates)==1){
            return date("Y-m-d H:i:s",$date_interval);
        }
        return [
            'begin_date'=>$timestamp?strtotime($dates[0]):$dates[0],
            'end_date'=>$timestamp?strtotime($dates[1]):$dates[1]
        ];
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findOneById($id)
    {
        $model = CommodityDemand::find()->asArray()
            ->select('id, commodity_name, t_id, p_id, brand, price, describe, status, create_time, checked_time, online_time')
            ->where(['id'=>$id])
            ->one();

        // 根据分类id查找分类名称
        $model1 = CommodityCategory::find()->asArray()
            ->all();
        $flag = 0;
        foreach ($model1 as $item) {
            if ($flag >= 2) {
                break;
            }
            if ($item['id'] == $model['t_id']) {
                $model['t_name'] = $item['name'];
                $flag += 1;
            } else if ($item['id'] == $model['p_id']) {
                $model['p_name'] = $item['name'];
                $flag += 1;
            }
        }

        // 时间格式化
        $model['submit_date'] = date('Y-m-d', $model['create_time']);
        $model['create_time'] = date('Y-m-d H:i:s', $model['create_time']);
        if ($model['checked_time'] != null) {
            $model['checked_date'] = date('Y-m-d', $model['checked_time']);
        }
        if ($model['online_time'] != null) {
            $model['online_date'] = date('Y-m-d', $model['online_time']);
        }

        return $model;
    }

}
