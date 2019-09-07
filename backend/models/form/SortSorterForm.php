<?php

namespace app\models\form;

use app\models\CommodityCategory;
use app\models\Customer;
use app\models\Model;
use app\models\SortSorter;
use Yii;
use yii\data\Pagination;

class SortSorterForm extends Model
{
    public $pageSize;
    public $pageNum;
    public $keyword;
    public $area_name;
    public $is_check;
    public $c_type;
    public $create_time;
    public $password;
    public $role = [
        '10' => '分拣员'
    ];

    public function rules()
    {
        return [
            [['keyword',], 'trim',],
            [['pageSize',], 'default', 'value' => 10,],
            [['pageNum',], 'default', 'value' => 0,],
            [['area_name',], 'default', 'value' => '',],
            [['create_time',], 'default', 'value' => ''],
            [['c_type',], 'default', 'value' => '10'],
            [['password',], 'default', 'value' => ''],
        ];
    }

    /**
     * 查询分拣员列表
     * @return array
     */
    public function search()
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = SortSorter::find()
            ->where(['type' => 3]);
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
     * 开关分拣员状态
     * @param $id
     */
    public function changeStatus($id)
    {
        $model = SortSorter::findOne($id);
        $model->is_check = (isset($model->is_check) && ($model->is_check == 0)) ? 1 : 0;
        if ($model->save()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 删除分拣员
     * @param $id
     */
    public function deleteUser($id)
    {
        $model = SortSorter::findOne($id);
        if ($model->delete()) {
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
        $model = $id ? SortSorter::findOne($id) : new SortSorter();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        $model->type = 3;
        $model->generateAuthKey();
        if (!$id) {
            $model->setPassword($this->password);
            $model->created_at = time();
            $model->mobile = $model->username;
            $model->email = 'sorter@moxiaoheng.com';
            $model->status = 10;
        } else {
            if (!empty($this->password)) $model->setPassword($this->password);
        }
        if ($model->save()) {
            $auth = \Yii::$app->authManager;
            if(!$id){
                $role = $auth->createRole('分拣员');
                $auth->assign($role, $model->id);
            }
            return true;
        }
        return $model->getErrorSummary(1)[0];
    }

    /**
     * 获取商品分类
     * @return mixed
     */
    public function getCommodityCate($id = 0)
    {
        $ids = [];
        if ($id) {
            $user = SortSorter::findOne($id)->toArray();
            $ids = explode(',', $user['sort_id']);
        }
        $model = CommodityCategory::find()->asArray()->all();
        $sortCate = [];
        foreach ($model as $k => &$v) {
            if (in_array($v['id'], $ids)) {
                $sortCate[] = $v;
                unset($model[$k]);
            }
        }
        $model = array_values($model);
        return ['dataCate' => $model, 'dataSort' => $sortCate];
    }

}
