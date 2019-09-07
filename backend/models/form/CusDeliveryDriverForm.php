<?php

namespace app\models\form;

use app\models\Model;
use common\models\UserDelivery;
use Yii;
use yii\data\Pagination;

class CusDeliveryDriverForm extends Model
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
     * 查询司机列表
     * @return array
     */
    public function search($storeId)
    {
        $this->attributes = Yii::$app->request->get();
        if (!$this->validate()) {
            return $this->getErrorResponse();
        }
        $query = UserDelivery::find()
            ->where(['type'=>1]);

        if ($storeId != null) {
            $query->andWhere(['store_id' => $storeId]);
        } else {
            $query->andWhere(['store_id' => \Yii::$app->user->identity['store_id']]);
        }

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
        foreach($list as &$v)
        {
            $v['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
        }
        return [
            'total' => $count,
            'list' => $list,
            'sql' => $this->getLastSql($query)
        ];
    }


    /**
     * 删除司机
     * @param $id
     */
    public function delete($id)
    {
        $model = UserDelivery::findOne($id);
        if ($model->delete()) {
            return true;
        }
        return $model->getErrors();
    }

    /**
     * 保存/新增司机信息
     * @param int $id
     * @return bool
     */
    public function save($id = 0, $storeId)
    {
        $model = $id ? UserDelivery::findOne($id) : new UserDelivery();
        $model->attributes = $this->attributes = Yii::$app->request->post();
        $model->store_id = $storeId;
        $model->generateAuthKey();
        if (!$id) {
            $model->setPassword($this->password);
            $model->created_at = time();
            $model->type = 1;
            $model->mobile = $model->username;
            $res = $model->save();
            $auth = \Yii::$app->authManager;
            $role = $auth->createRole('门店配送员');
            $auth->assign($role, $model->id);
            if ($res) {
                return true;
            }
        }else{
            if (!empty($this->password)) $model->setPassword($this->password);
            if($model->save()){
                return true;
            }

        }
        return $model->getErrorSummary(1)[0];
    }


}
