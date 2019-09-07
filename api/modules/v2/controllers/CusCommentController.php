<?php

namespace api\modules\v2\controllers;


use app\models\form\CusCommentForm;

class CusCommentController extends Controller
{


    /**
     * 评论列表
     * @return array
     */
    public function actionList($commodity_id = 0){
        $model = new CusCommentForm();
        return $model->search($commodity_id);
    }


    /**
     * 保存评论
     * @return array
     */
    public function actionSave(){
        $model = new CusCommentForm();
        $model->user = $this->user;
        return $model->save();
    }

}