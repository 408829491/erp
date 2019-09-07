<?php

namespace app\models\form;

use app\models\Admin;
use app\models\AuthAssignment;
use app\models\Model;
use rbac\models\form\Signup;
use rbac\models\searchs\User;
use yii\data\Pagination;

/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/5/23
 * Time: 15:12
 */

class CusStoreForm extends Model
{
    // C端门店分页
    public function findPage($pageNum, $pageSize, $filterProperty)
    {
        $pageNum -= 1;

        $query = \app\models\CusStore::find()->asArray();

        // 查询条件
        if ($filterProperty != null) {
            $json = json_decode($filterProperty,true);
            $searchText = isset($json['searchText']) ? $json['searchText'] : null;
            if ($searchText != null) {
                $query->andWhere(['like', 'name', "%$searchText%", false]);
            }
        }

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'page' => $pageNum, 'pageSize' => $pageSize]);

        $data['total'] = $count;
        $tempData = $query->asArray()
            ->orderBy("id DESC")
            ->offset($pagination->offset)
            ->limit($pagination->pageSize)
            ->all();

        $data['list'] = $tempData;
        return $data;
    }

    // TODO 开启事务
    public function saveData($model1, $username, $password)
    {
        $model1->save();

        // 注册用户
        $admin = new Admin();
        $admin->username = $username;
        $admin->nickname = $username;
        $admin->email = 'test@qq.com';
        $admin->password_hash = \Yii::$app->security->generatePasswordHash($password);
        $admin->auth_key = \Yii::$app->security->generateRandomString();
        $admin->store_id = $model1->id;
        $admin->created_at = time();
        $admin->updated_at = time();
        $admin->save();

        // 添加C端管理员权限
        $auth = \Yii::$app->authManager;
        $role = $auth->createRole('C端管理员');
        $auth->assign($role, $admin->id);
    }
}