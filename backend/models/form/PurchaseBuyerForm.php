<?php

namespace app\models\form;

use app\models\Customer;
use app\models\DeliveryDriver;
use app\models\DeliveryLine;
use app\models\Model;
use app\models\Order;
use app\models\PurchaseBuyer;
use app\models\Salesman;
use common\models\User;
use Yii;
use yii\data\Pagination;

class PurchaseBuyerForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $area_name;
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
            [['password',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询采员购列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = PurchaseBuyer::find()
            ->where(['type' => 4]);
        if ($this->keyword) {
            $query->andwhere([
                'or',
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
        foreach ($list as &$v) {
            $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 删除采购员
     * @param $id
     */
    public function delete($id)
    {
        $model = PurchaseBuyer::findOne($id);
        if ($model->delete()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 保存/新增采购员信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0)
    {
        $model = $id ? PurchaseBuyer::findOne($id) : new PurchaseBuyer();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        $model->type = 4;
        $model->generateAuthKey();
        if (!$id) {
            $model->setPassword($this->password);
            $model->created_at = time();
            $model->mobile = $model->username;
            $model->email = 'buyer@moxiaoheng.com';
            $model->status = 10;
        } else {
            if (!empty($this->password)) $model->setPassword($this->password);
        }
        if ($model->save()) {
            $auth = \Yii::$app->authManager;
            $role = $auth->createRole('采购员');
            $auth->assign($role, $model->id);
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }


}
