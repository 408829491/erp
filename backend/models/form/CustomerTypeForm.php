<?php

namespace app\models\form;

use app\models\Customer;
use app\models\CustomerType;
use app\models\Model;
use Yii;
use yii\data\Pagination;

class CustomerTypeForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $status;
    public $is_check;
    public $source;
    public $create_time;
    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['is_check',], 'default', 'value' => '',],
            [['create_time',], 'default','value'=>''],
        ];
    }

    /**
     * 查询订单列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = CustomerType::find();
        if($this->status){
            $query->andWhere([
                'is_check'=>$this->is_check
            ]);
        }
        if($this->keyword) {
            $query->andwhere(
                ['like','name',$this->keyword]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1,'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list,function(&$data){
            $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

}
