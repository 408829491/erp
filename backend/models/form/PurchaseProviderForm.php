<?php

namespace app\models\form;

use app\models\Commodity;
use app\models\Customer;
use app\models\DeliveryDriver;
use app\models\DeliveryLine;
use app\models\Model;
use app\models\Order;
use app\models\PurchaseProvider;
use app\models\Salesman;
use common\models\User;
use Yii;
use yii\data\Pagination;

class PurchaseProviderForm extends Model
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
     * 查询供应商列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = PurchaseProvider::find()
            ->where(['<>', 'is_delete', '1']);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'name', $this->keyword],
                ['like', 'mobile', $this->keyword],
                ['like', 'contact_name', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit($pagination->pageSize)
            ->all();
        foreach($list as &$v)
        {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 删除供应商
     * @param $id
     */
    public function delete($id)
    {
        $model = PurchaseProvider::findOne($id);
        $model->is_delete = 1;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 保存/新增供应商信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0)
    {
        $model = $id ? PurchaseProvider::findOne($id) : new PurchaseProvider();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        if (!$id) {
            $model->create_time = time();
            $model->password = Yii::$app->security->generatePasswordHash($this->password);
        } else {
            if (!empty($this->password)) $model->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        if ($model->save()) {
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }


    /**
     * 供应商商品列表
     * @return array
     */
    public function getProviderCommodity()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = Commodity::find()
            ->where(['<>', 'is_online', '1']);
        if ($this->keyword) {
            $query->andwhere([
                'or',
                ['like', 'name', $this->keyword],
            ]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'page' => $this->pageNum - 1, 'pageSize' => $this->pageSize]);
        $list = $query->asArray()
            ->select('id,name')
            ->offset($pagination->offset)
            ->orderBy('id DESC')
            ->limit(500)
            ->all();
        return [
            'dataCate' => $list,
            'dataSort' => [],
        ];
    }

}
