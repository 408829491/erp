<?php

namespace app\models\form;

use app\models\Customer;
use app\models\Model;
use Yii;
use yii\data\Pagination;

class CustomerForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $area_name;
    public $is_check;
    public $c_type;
    public $create_time;
    public $password;

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['area_name',], 'default', 'value' => '',],
            [['create_time',], 'default', 'value' => ''],
            [['c_type',], 'default', 'value' => ''],
            [['password',], 'default', 'value' => ''],
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
        $query = Customer::find()
            ->where(['<>', 'is_deleted', '1']);
        if ($this->c_type) {
            $query->andWhere([
                'c_type' => $this->c_type
            ]);
        }
        if ($this->area_name) {
            $query->andWhere([
                'area_name' => $this->area_name
            ]);
        }
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'username', $this->keyword],
                ['like', 'nickname', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        array_walk($list, function (&$data) {
            $data['created_at'] = date('Y-m-d H:i:s', $data['created_at']);
        });
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 开关客户状态
     * @param $id
     */
    public function changeStatus($id)
    {
        $model = Customer::findOne($id);
        $model->is_check = (isset($model->is_check) && ($model->is_check == 0)) ? 1 : 0;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 删除客户
     * @param $id
     */
    public function deleteUser($id)
    {
        $model = Customer::findOne($id);
        $model->is_deleted = 1;
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
            $model->mobile = $model->username;
            $model->status = 10;
            $model->r_id = 3;
        }else{
            if (!empty($this->password)) $model->setPassword($this->password);
        }
        $model->is_pay_on = $model->is_pay_on == 'on' ? 1 : 0;
        $model->is_check = $model->is_check == 'on' ? 1 : 0;
        if ($model->save()) {
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }


}
