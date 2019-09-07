<?php

namespace app\models\form;

use app\models\Customer;
use app\models\Model;
use app\models\Order;
use app\models\Salesman;
use common\models\User;
use Yii;
use yii\data\Pagination;

class SalesmanForm extends Model
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
        $query = Salesman::find()
            ->where(['<>', 'is_delete', '1']);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'name', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        $promoteCount = $this->promoteCount();
        $saleCount = $this->salePerformanceCount();
        $index = 'invitation_code';
        foreach($list as &$v)
        {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['num'] =  ($v[$index]&&isset($promoteCount[$v[$index]]['num']))?$promoteCount[$v[$index]]['num']:0;
            $v['sale_num'] =  ($v[$index]&&isset($saleCount[$v[$index]]['num']))?$saleCount[$v[$index]]['num']:0;
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }

    /**
     * 获取推广人数统计
     * @return mixed
     */
    public function promoteCount()
    {
        $model = User::find();
        $query = $model
            ->select('invite_code,count(id) as num')
            ->groupBy('invite_code')
            ->indexBy('invite_code')
            ->asArray()
            ->all();
        return $query;
    }


    /**
     * 获取推广用户列表
     * @return mixed
     */
    public function getCustomerList($code)
    {
        $model = User::find();
        $model->where(['invite_code'=>$code]);
        $count = $model->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $model->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as &$v)
        {
            $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
        }
        return [
            'total' => $count,
            'list' => $list,
        ];
    }

    /**
     * 销售业绩统计
     * @return mixed
     */
    public function salePerformanceCount()
    {
        $model = Order::find();
        $query = $model
            ->select('invite_code,count(order_no) as num')
            ->join('LEFT JOIN','bn_user','bn_user.id=bn_order.user_id')
            ->groupBy('user_id')
            ->indexBy('invite_code')
            ->asArray()
            ->all();
        return $query;
    }

    /**
     * 开关销售员状态
     * @param $id
     */
    public function changeStatus($id)
    {
        $model = Salesman::findOne($id);
        $model->lock_status = (isset($model->lock_status) && ($model->lock_status == 0)) ? 1 : 0;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 删除销售员
     * @param $id
     */
    public function deleteUser($id)
    {
        $model = Salesman::findOne($id);
        $model->is_delete = 1;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 保存/新增销售员信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0)
    {
        $model = $id ? Salesman::findOne($id) : new Salesman();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        if (!$id) {
            $model->password = Yii::$app->security->generatePasswordHash($this->password);
            $model->create_time = time();
            $model->lock_status = 1;
            $inviteCode = $this->createCode();
            $model->invitation_code = $inviteCode;
        } else {
            if (!empty($this->password)) $model->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        if ($model->save()) {
            $model->invitation_code = 'BN' . ($model->id + 200);
            $model->save();
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }

    /**
     * 生成随机邀请码
     * @param $userId
     * @return string
     */
    private function createCode()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;
        return $d;
    }


}
